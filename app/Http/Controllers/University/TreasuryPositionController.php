<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankMovement;
use App\Models\CashRegister;
use App\Models\ExpensePayment;
use App\Models\Payment;
use Illuminate\Http\Request;

class TreasuryPositionController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input(
            'date',
            now()->toDateString()
        );

        /*
        |--------------------------------------------------------------------------
        | Caixa
        |--------------------------------------------------------------------------
        |
        | O módulo de caixa atual trabalha sem campo de moeda.
        | Por isso tratamos o caixa como AOA.
        |
        */

        $cashRegisters = CashRegister::query()
            ->with('user')
            ->where('status', 'open')
            ->orderBy('opened_at')
            ->get();

        $cashBalance = $cashRegisters->sum(function ($register) {
            return (float) ($register->expected_balance ?? 0);
        });

        /*
        |--------------------------------------------------------------------------
        | Bancos
        |--------------------------------------------------------------------------
        */

        $bankAccounts = BankAccount::query()
            ->where('is_active', true)
            ->orderBy('currency')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Bancos agrupados por moeda
        |--------------------------------------------------------------------------
        */

        $bankByCurrency = $bankAccounts
            ->groupBy(fn ($account) => strtoupper($account->currency))
            ->map(function ($accounts, $currency) {
                return [
                    'currency' => $currency,
                    'accounts' => $accounts,
                    'balance' => $accounts->sum(
                        fn ($account) => (float) $account->current_balance
                    ),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Totais de movimentos bancários do dia
        |--------------------------------------------------------------------------
        */

        $bankMovements = BankMovement::query()
            ->with('bankAccount')
            ->whereDate('movement_date', $date)
            ->get();

        $bankDaily = $bankMovements
            ->groupBy(function ($movement) {
                return strtoupper(
                    $movement->bankAccount->currency ?? 'AOA'
                );
            })
            ->map(function ($movements, $currency) {

                $income = 0;
                $expense = 0;

                foreach ($movements as $movement) {

                    if ($movement->isIncome()) {
                        $income += (float) $movement->amount;
                    }

                    if ($movement->isExpense()) {
                        $expense += (float) $movement->amount;
                    }
                }

                return [
                    'currency' => $currency,
                    'income' => $income,
                    'expense' => $expense,
                    'net' => $income - $expense,
                    'count' => $movements->count(),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Recebimentos do dia
        |--------------------------------------------------------------------------
        */

        $receipts = Payment::query()
            ->where('status', 'confirmed')
            ->whereDate('payment_date', $date)
            ->get();

        $receiptsByCurrency = $receipts
            ->groupBy(fn ($payment) => strtoupper(
                $payment->currency ?? 'AOA'
            ))
            ->map(function ($payments, $currency) {

                return [
                    'currency' => $currency,
                    'total' => $payments->sum(
                        fn ($payment) => (float) $payment->amount
                    ),
                    'count' => $payments->count(),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Pagamentos de despesas do dia
        |--------------------------------------------------------------------------
        */

        $expensePayments = ExpensePayment::query()
            ->where('status', 'confirmed')
            ->whereDate('payment_date', $date)
            ->get();

        $expensesByCurrency = $expensePayments
            ->groupBy(fn ($payment) => strtoupper(
                $payment->currency ?? 'AOA'
            ))
            ->map(function ($payments, $currency) {

                return [
                    'currency' => $currency,
                    'total' => $payments->sum(
                        fn ($payment) => (float) $payment->amount
                    ),
                    'count' => $payments->count(),
                ];
            });

        /*
        |--------------------------------------------------------------------------
        | Moedas existentes
        |--------------------------------------------------------------------------
        */

        $currencies = collect()
            ->merge($bankByCurrency->keys())
            ->merge($bankDaily->keys())
            ->merge($receiptsByCurrency->keys())
            ->merge($expensesByCurrency->keys())
            ->push('AOA')
            ->unique()
            ->sort()
            ->values();

        return view(
            'university.finance.treasury.position.index',
            compact(
                'date',
                'cashRegisters',
                'cashBalance',
                'bankAccounts',
                'bankByCurrency',
                'bankDaily',
                'receiptsByCurrency',
                'expensesByCurrency',
                'currencies'
            )
        );
    }
}
