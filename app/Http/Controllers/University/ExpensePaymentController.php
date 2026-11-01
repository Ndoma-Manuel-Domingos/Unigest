<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\Expense;
use App\Models\ExpensePayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ExpensePaymentController extends Controller
{
    public function create(string $expense)
    {
        $expense = Expense::findOrFail($expense); 

        abort_unless(
            in_array($expense->status, ['approved', 'partially_paid']),
            403,
            'Esta despesa não pode receber pagamentos.'
        );

        abort_if((float) $expense->balance <= 0, 403);

        $expense->load('supplier', 'category', 'payments.user');

        return view('university.finance.expenses.payments.create', compact('expense'));
    }

    public function store(Request $request, string $expense)
    {
        $expense = Expense::findOrFail($expense);

        $validated = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => [
                'required',
                Rule::in([
                    'cash',
                    'bank_transfer',
                    'multicaixa',
                    'pos',
                    'reference',
                    'other',
                ]),
            ],
            'transaction_reference' => ['nullable', 'string', 'max:100'],
            'observations' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($validated, $expense) {
            $expense = Expense::whereKey($expense->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (!in_array($expense->status, ['approved', 'partially_paid'])) {
                throw ValidationException::withMessages([
                    'amount' => 'Esta despesa não está aprovada para pagamento.',
                ]);
            }

            $balance = (float) $expense->balance;
            $amount = (float) $validated['amount'];

            if ($amount > $balance) {
                throw ValidationException::withMessages([
                    'amount' => 'O valor do pagamento não pode ser superior ao saldo da despesa.',
                ]);
            }

            $cashRegister = null;

            if ($validated['payment_method'] === 'cash') {
                $cashRegister = CashRegister::where('user_id', auth()->id())
                    ->where('status', 'open')
                    ->lockForUpdate()
                    ->first();

                if (!$cashRegister) {
                    throw ValidationException::withMessages([
                        'payment_method' => 'Para receber um pagamento em dinheiro, deve abrir primeiro a sua caixa.',
                    ]);
                }
            }

            $lastPayment = ExpensePayment::whereYear(
                'created_at',
                now()->year
            )
                ->lockForUpdate()
                ->orderByDesc('id')
                ->first();

            $sequence = $lastPayment
                ? ((int) substr($lastPayment->payment_number, -6)) + 1
                : 1;

            $paymentNumber = 'PGD-' . now()->format('Y') . '-' .
                str_pad($sequence, 6, '0', STR_PAD_LEFT);

            $payment = ExpensePayment::create([
                'tenant_id' => $expense->tenant_id,
                'expense_id' => $expense->id,
                'cash_register_id' => $cashRegister?->id,
                'user_id' => auth()->id(),
                'payment_number' => $paymentNumber,
                'payment_date' => $validated['payment_date'],
                'amount' => $amount,
                'currency' => $expense->currency,
                'payment_method' => $validated['payment_method'],
                'transaction_reference' => $validated['transaction_reference'] ?? null,
                'status' => 'confirmed',
                'observations' => $validated['observations'] ?? null,
            ]);

            if ($cashRegister) {
                $movement = CashMovement::create([
                    'tenant_id' => $expense->tenant_id,
                    'cash_register_id' => $cashRegister->id,
                    'user_id' => auth()->id(),
                    'type' => 'expense',
                    'source' => 'expense_payment',
                    'description' => 'Pagamento da despesa ' . $expense->expense_number,
                    'amount' => $amount,
                    'movement_date' => $validated['payment_date'],
                    'observations' => 'Pagamento ' . $paymentNumber,
                ]);

                $payment->update([
                    'cash_movement_id' => $movement->id,
                ]);
            }

            $totalPaid = (float) ExpensePayment::where('expense_id', $expense->id)
                ->where('status', 'confirmed')
                ->sum('amount');

            $newBalance = max(0, (float) $expense->amount - $totalPaid);

            $expense->paid_amount = $totalPaid;
            $expense->balance = $newBalance;
            $expense->status = $newBalance <= 0 ? 'paid' : 'partially_paid';
            $expense->paid_at = $newBalance <= 0 ? now() : null;
            $expense->payment_method = $validated['payment_method'];
            $expense->save();
        });

        return redirect()
            ->route('university.expenses.show', $expense)
            ->with('success', 'Pagamento registado com sucesso.');
    }
}
