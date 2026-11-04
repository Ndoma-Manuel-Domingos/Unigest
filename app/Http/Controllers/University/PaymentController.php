<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

use App\Services\Finance\FinancialAuditService;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['invoice.student', 'receivedBy'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('payment_number', 'ILIKE', "%{$search}%")
                        ->orWhere('transaction_reference', 'ILIKE', "%{$search}%")
                        ->orWhereHas('invoice', function ($invoice) use ($search) {
                            $invoice->where('invoice_number', 'ILIKE', "%{$search}%")
                                ->orWhereHas('student', function ($student) use ($search) {
                                    $student->where('name', 'ILIKE', "%{$search}%")
                                        ->orWhere('student_number', 'ILIKE', "%{$search}%");
                                });
                        });
                });
            })
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->filled('payment_method'), fn ($q) =>
                $q->where('payment_method', $request->payment_method)
            )
            ->when($request->filled('date_from'), fn ($q) =>
                $q->whereDate('payment_date', '>=', $request->date_from)
            )
            ->when($request->filled('date_to'), fn ($q) =>
                $q->whereDate('payment_date', '<=', $request->date_to)
            )
            ->latest('payment_date')
            ->paginate(20)
            ->withQueryString();

        return view('university.finance.payments.index', compact('payments'));
    }

    public function create(string $invoice)
    {
        $invoice = Invoice::findOrFail($invoice); 

        if (! in_array($invoice->status, ['issued', 'partially_paid', 'overdue'], true)
            || (float) $invoice->balance <= 0) {
            return redirect()
                ->route('university.invoices.show', $invoice)
                ->with('error', 'Esta factura não permite novos pagamentos.');
        }

        return view('university.finance.payments.create', compact('invoice'));
    }

    public function store(Request $request, string $invoice, FinancialAuditService $audit)
    {
        $invoice = Invoice::findOrFail($invoice); 

        $data = $request->validate([
            'payment_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:999999999999.99'],
            'payment_method' => [
                'required',
                'in:cash,bank_transfer,multicaixa,pos,reference,other',
            ],
            'transaction_reference' => ['nullable', 'string', 'max:150'],
            'observations' => ['nullable', 'string', 'max:2000'],
        ]);

        $payment = DB::transaction(function () use ($invoice, $data, $audit) {
            $lockedInvoice = Invoice::whereKey($invoice->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! in_array($lockedInvoice->status, ['issued', 'partially_paid', 'overdue'], true)
                || (float) $lockedInvoice->balance <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'A factura não está disponível para receber pagamentos.',
                ]);
            }

            if ((float) $data['amount'] > (float) $lockedInvoice->balance) {
                throw ValidationException::withMessages([
                    'amount' => 'O valor do pagamento não pode ser superior ao saldo em dívida.',
                ]);
            }

            $tenantId = auth()->user()->tenant_id;

            University::whereKey($tenantId)
                ->lockForUpdate()
                ->firstOrFail();

            $prefix = 'REC-' . now()->format('Y') . '-';

            $number = Payment::where('payment_number', 'ILIKE', $prefix . '%')
                ->count() + 1;

            $paymentNumber = $prefix . str_pad($number, 6, '0', STR_PAD_LEFT);

            $payment = Payment::create([
                'invoice_id' => $lockedInvoice->id,
                'payment_number' => $paymentNumber,
                'payment_date' => $data['payment_date'],
                'amount' => $data['amount'],
                'currency' => $lockedInvoice->currency,
                'payment_method' => $data['payment_method'],
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'status' => 'pending',
                'observations' => $data['observations'] ?? null,
                'received_by' => auth()->id(),
            ]);

            $audit->record(
                module: 'payments',
                event: 'payment.created',
                description: 'Foi registado um pagamento de factura.',
                record: $payment,
                newValues: [
                    'invoice_id' => $payment->invoice_id,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                ]
            );

            return $payment;
        });

        return redirect()
            ->route('university.payments.index')
            ->with('success', 'Pagamento registado como pendente. Confirme o recebimento após verificar o valor.');
    }

    public function confirm(string $payment)
    {
        $payment = Payment::findOrFail($payment); 

        DB::transaction(function () use ($payment) {
            $lockedPayment = Payment::whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $invoice = Invoice::whereKey($lockedPayment->invoice_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPayment->status !== 'pending') {
                throw ValidationException::withMessages([
                    'payment' => 'Este pagamento já não está pendente.',
                ]);
            }

            if (! in_array($invoice->status, ['issued', 'partially_paid', 'overdue'], true)) {
                throw ValidationException::withMessages([
                    'payment' => 'A factura não permite confirmar pagamentos.',
                ]);
            }

            if ((float) $lockedPayment->amount > (float) $invoice->balance) {
                throw ValidationException::withMessages([
                    'payment' => 'O valor do pagamento ultrapassa o saldo actual da factura.',
                ]);
            }

            $lockedPayment->update(['status' => 'confirmed']);

            if ($lockedPayment->payment_method === 'cash') {
                $cashRegister = CashRegister::where('user_id', auth()->id())
                    ->where('status', 'open')
                    ->lockForUpdate()
                    ->first();

                if (!$cashRegister) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'payment' => 'Abre o teu caixa antes de confirmar um pagamento em dinheiro.',
                    ]);
                }

                if ($cashRegister) {
                    CashMovement::create([
                        'cash_register_id' => $cashRegister->id,
                        'user_id' => auth()->id(),
                        'payment_id' => $lockedPayment->id,
                        'type' => 'income',
                        'source' => 'payment',
                        'description' => 'Pagamento da factura ' . $invoice->invoice_number,
                        'amount' => $lockedPayment->amount,
                        'movement_date' => $lockedPayment->payment_date,
                        'observations' => 'Recebimento automático do módulo de pagamentos.',
                    ]);
                }
            }

            $invoice->recalculateFinancials();
        });

        return back()->with('success', 'Pagamento confirmado com sucesso.');
    }

    public function cancel(string $payment)
    {
        $payment = Payment::findOrFail($payment); 


        if ($payment->status !== 'pending') {
            return back()->with('error', 'Apenas pagamentos pendentes podem ser anulados. Para pagamentos confirmados, utilize a reversão.');
        }

        $payment->update(['status' => 'cancelled']);

        return back()->with('success', 'Pagamento pendente anulado.');
    }

    public function reverse(Request $request, string $payment)
    {
        $payment = Payment::findOrFail($payment); 


        $data = $request->validate([
            'observations' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        DB::transaction(function () use ($payment, $data) {
            $lockedPayment = Payment::whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            $invoice = Invoice::whereKey($lockedPayment->invoice_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPayment->status !== 'confirmed') {
                throw ValidationException::withMessages([
                    'payment' => 'Só é possível reverter pagamentos confirmados.',
                ]);
            }

            if ($lockedPayment->payment_method === 'cash') {
                $cashRegister = CashRegister::where('user_id', auth()->id())
                    ->where('status', 'open')
                    ->lockForUpdate()
                    ->first();

                if (!$cashRegister) {
                    throw ValidationException::withMessages([
                        'payment' => 'Abra um caixa antes de reverter um pagamento em dinheiro.',
                    ]);
                }

                CashMovement::create([
                    'cash_register_id' => $cashRegister->id,
                    'user_id' => auth()->id(),
                    'reference_payment_id' => $lockedPayment->id,
                    'type' => 'expense',
                    'source' => 'payment_reversal',
                    'description' => 'Devolução do pagamento ' . $lockedPayment->payment_number,
                    'amount' => $lockedPayment->amount,
                    'movement_date' => now(),
                    'observations' => $data['observations'],
                ]);
            }

            $lockedPayment->update([
                'status' => 'reversed',
                'observations' => trim(
                    ($lockedPayment->observations ? $lockedPayment->observations . "\n" : '')
                    . 'Reversão: ' . $data['observations']
                ),
            ]);

            $invoice->recalculateFinancials();
        });

        return back()->with('success', 'Pagamento revertido. O saldo da factura foi actualizado.');
    }

    public function receipt(string $payment)
    {
        $payment = Payment::findOrFail($payment); 

        if ($payment->status !== 'confirmed') {
            return redirect()
                ->route('university.payments.index')
                ->with('error', 'O recibo só pode ser consultado para pagamentos confirmados.');
        }

        $payment->load([
            'invoice.student',
            'invoice.enrollment.course',
            'invoice.enrollment.academicYear',
            'receivedBy',
        ]);

        return view('university.finance.payments.receipt', compact('payment'));
    }
}
