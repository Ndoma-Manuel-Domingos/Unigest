<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\BankMovement;
use App\Models\CashMovement;
use App\Models\ExpensePayment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TreasuryMovementController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        abort_unless($tenantId, 403, 'Universidade não identificada.');

        $filters = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'type' => ['nullable', 'in:income,expense'],
            'source' => ['nullable', 'in:receipt,expense_payment,cash,bank'],
        ]);

        $dateFrom = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $filters['date_to'] ?? now()->toDateString();

        $movements = collect();

        /*
         * 1. RECEBIMENTOS DE ESTUDANTES
         * A tabela payments é a fonte principal dos recebimentos.
         */
        if (empty($filters['source']) || $filters['source'] === 'receipt') {
            $payments = Payment::query()
                ->where('tenant_id', $tenantId)
                ->where('status', 'confirmed')
                ->whereDate('payment_date', '>=', $dateFrom)
                ->whereDate('payment_date', '<=', $dateTo)
                ->with(['invoice'])
                ->get();

            foreach ($payments as $payment) {
                $movements->push((object) [
                    'date' => $payment->payment_date,
                    'reference' => $payment->transaction_reference
                        ?? $payment->id,
                    'description' => 'Recebimento de factura '
                        . ($payment->invoice?->invoice_number ?? ''),
                    'source' => 'receipt',
                    'source_label' => 'Recebimento',
                    'type' => 'income',
                    'amount' => (float) $payment->amount,
                    'currency' => $payment->currency ?? 'AOA',
                    'user' => null,
                    'url' => null,
                ]);
            }
        }

        /*
         * 2. PAGAMENTOS DE DESPESAS
         */
        if (empty($filters['source']) || $filters['source'] === 'expense_payment') {
            $expensePayments = ExpensePayment::query()
                ->where('tenant_id', $tenantId)
                ->where('status', 'confirmed')
                ->whereDate('payment_date', '>=', $dateFrom)
                ->whereDate('payment_date', '<=', $dateTo)
                ->with(['expense', 'user'])
                ->get();

            foreach ($expensePayments as $payment) {
                $movements->push((object) [
                    'date' => $payment->payment_date,
                    'reference' => $payment->payment_number,
                    'description' => 'Pagamento de despesa '
                        . ($payment->expense?->expense_number ?? ''),
                    'source' => 'expense_payment',
                    'source_label' => 'Pagamento de despesa',
                    'type' => 'expense',
                    'amount' => (float) $payment->amount,
                    'currency' => $payment->currency ?? 'AOA',
                    'user' => $payment->user?->name,
                    'url' => null,
                ]);
            }
        }

        /*
         * 3. MOVIMENTOS MANUAIS DE CAIXA
         * Os movimentos automáticos de pagamentos são excluídos
         * para evitar duplicação com payments e expense_payments.
         */
        if (empty($filters['source']) || $filters['source'] === 'cash') {
            $cashMovements = CashMovement::query()
                ->where('tenant_id', $tenantId)
                ->where('source', 'manual')
                ->whereDate('movement_date', '>=', $dateFrom)
                ->whereDate('movement_date', '<=', $dateTo)
                ->with(['user', 'cashRegister'])
                ->get();

            foreach ($cashMovements as $movement) {
                $movements->push((object) [
                    'date' => $movement->movement_date,
                    'reference' => $movement->id,
                    'description' => $movement->description,
                    'source' => 'cash',
                    'source_label' => 'Movimento manual de caixa',
                    'type' => $movement->type,
                    'amount' => (float) $movement->amount,
                    'currency' => 'AOA',
                    'user' => $movement->user?->name,
                    'url' => null,
                ]);
            }
        }

        /*
         * 4. MOVIMENTOS BANCÁRIOS
         */
        if (empty($filters['source']) || $filters['source'] === 'bank') {
            $bankMovements = BankMovement::query()
                ->where('tenant_id', $tenantId)
                ->whereDate('movement_date', '>=', $dateFrom)
                ->whereDate('movement_date', '<=', $dateTo)
                ->with(['bankAccount', 'user'])
                ->get();

            foreach ($bankMovements as $movement) {
                $type = match ($movement->type) {
                    'income', 'transfer_in' => 'income',
                    'expense', 'transfer_out' => 'expense',
                    'adjustment' => $movement->adjustment_direction === 'debit'? 'expense': 'income',
                    default => null,
                };

                if (!$type) {
                    continue;
                }

                // $type = $movement->isIncome() ? 'income' : ($movement->isExpense() ? 'expense' : null);

                // if (!$type) {
                //     continue;
                // }

                $movements->push((object) [
                    'date' => $movement->movement_date,
                    'reference' => $movement->reference
                        ?? $movement->id,
                    'description' => $movement->description
                        . ' — ' . ($movement->bankAccount?->name ?? 'Conta bancária'),
                    'source' => 'bank',
                    'source_label' => 'Movimento bancário',
                    'type' => $type,
                    'amount' => (float) $movement->amount,
                    'currency' => $movement->bankAccount?->currency ?? 'AOA',
                    'user' => $movement->user?->name,
                    'url' => null,
                ]);
            }
        }

        // Filtros finais
        if (!empty($filters['type'])) {
            $movements = $movements
                ->where('type', $filters['type']);
        }

        $movements = $movements
            ->sortByDesc(fn ($item) => $item->date)
            ->values();

        $totalIncome = $movements
            ->where('type', 'income')
            ->groupBy('currency')
            ->map(fn ($items) => $items->sum('amount'));

        $totalExpense = $movements
            ->where('type', 'expense')
            ->groupBy('currency')
            ->map(fn ($items) => $items->sum('amount'));

        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;

        $paginatedMovements = new LengthAwarePaginator(
            $movements->forPage($page, $perPage)->values(),
            $movements->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('university.finance.treasury.movements.index', [
            'movements' => $paginatedMovements,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'selectedType' => $filters['type'] ?? '',
            'selectedSource' => $filters['source'] ?? '',
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
        ]);
    }
}
