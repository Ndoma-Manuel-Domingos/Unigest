<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\ExpensePayment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TreasuryCashFlowController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->input(
            'date_from',
            now()->startOfMonth()->toDateString()
        );

        $dateTo = $request->input(
            'date_to',
            now()->toDateString()
        );

        $type = $request->input('type');
        $method = $request->input('method');
        $currency = $request->input('currency');
        $search = trim($request->input('search', ''));

        $items = collect();

        /*
        |--------------------------------------------------------------------------
        | RECEBIMENTOS
        |--------------------------------------------------------------------------
        */

        if (!$type || $type === 'income') {

            $payments = Payment::query()
                ->with([
                    'invoice.student',
                    'user',
                ])
                ->where('status', 'confirmed')
                ->whereDate('payment_date', '>=', $dateFrom)
                ->whereDate('payment_date', '<=', $dateTo)
                ->when($method, function ($query) use ($method) {
                    $query->where('payment_method', $method);
                })
                ->when($currency, function ($query) use ($currency) {
                    $query->where('currency', $currency);
                })
                ->when($search, function ($query) use ($search) {

                    $query->where(function ($q) use ($search) {

                        $q->where('payment_number', 'ILIKE', "%{$search}%")
                            ->orWhere('transaction_reference', 'ILIKE', "%{$search}%");

                    });
                })
                ->get();

            foreach ($payments as $payment) {

                $student = $payment->invoice?->student;

                $items->push([
                    'date' => $payment->payment_date,
                    'type' => 'income',
                    'document' => $payment->payment_number ?? $payment->id,
                    'description' => 'Recebimento de estudante',
                    'party' => $student?->name
                        ?? $student?->nome
                        ?? '—',
                    'method' => $payment->payment_method ?? '—',
                    'reference' => $payment->transaction_reference ?? '—',
                    'currency' => strtoupper($payment->currency ?? 'AOA'),
                    'amount' => (float) $payment->amount,
                    'operator' => $payment->user?->name ?? '—',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | PAGAMENTOS
        |--------------------------------------------------------------------------
        */

        if (!$type || $type === 'expense') {

            $payments = ExpensePayment::query()
                ->with([
                    'expense.supplier',
                    'user',
                ])
                ->where('status', 'confirmed')
                ->whereDate('payment_date', '>=', $dateFrom)
                ->whereDate('payment_date', '<=', $dateTo)
                ->when($method, function ($query) use ($method) {
                    $query->where('payment_method', $method);
                })
                ->when($currency, function ($query) use ($currency) {
                    $query->where('currency', $currency);
                })
                ->when($search, function ($query) use ($search) {

                    $query->where(function ($q) use ($search) {

                        $q->where('payment_number', 'ILIKE', "%{$search}%")
                            ->orWhere('transaction_reference', 'ILIKE', "%{$search}%");

                    });
                })
                ->get();

            foreach ($payments as $payment) {

                $supplier = $payment->expense?->supplier;

                $items->push([
                    'date' => $payment->payment_date,
                    'type' => 'expense',
                    'document' => $payment->payment_number ?? $payment->id,
                    'description' => $payment->expense?->description
                        ?? 'Pagamento de despesa',
                    'party' => $supplier?->name ?? '—',
                    'method' => $payment->payment_method ?? '—',
                    'reference' => $payment->transaction_reference ?? '—',
                    'currency' => strtoupper($payment->currency ?? 'AOA'),
                    'amount' => (float) $payment->amount,
                    'operator' => $payment->user?->name ?? '—',
                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ORDENAÇÃO
        |--------------------------------------------------------------------------
        */

        $items = $items
            ->sortByDesc(function ($item) {
                return strtotime((string) $item['date']);
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | TOTAIS POR MOEDA
        |--------------------------------------------------------------------------
        */

        $summary = $items
            ->groupBy('currency')
            ->map(function (Collection $rows, $currency) {

                $income = $rows
                    ->where('type', 'income')
                    ->sum('amount');

                $expense = $rows
                    ->where('type', 'expense')
                    ->sum('amount');

                return [
                    'currency' => $currency,
                    'income' => $income,
                    'expense' => $expense,
                    'net' => $income - $expense,
                    'count' => $rows->count(),
                ];
            })
            ->sortKeys();

        /*
        |--------------------------------------------------------------------------
        | PAGINAÇÃO
        |--------------------------------------------------------------------------
        */

        $perPage = 30;

        $page = LengthAwarePaginator::resolveCurrentPage();

        $results = new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view(
            'university.finance.treasury.reports.cash-flow',
            compact(
                'results',
                'summary',
                'dateFrom',
                'dateTo',
                'type',
                'method',
                'currency',
                'search'
            )
        );
    }

    public function print(Request $request)
    {
        $request->merge([
            'type' => $request->input('type'),
        ]);

        $dateFrom = $request->input(
            'date_from',
            now()->startOfMonth()->toDateString()
        );

        $dateTo = $request->input(
            'date_to',
            now()->toDateString()
        );

        $type = $request->input('type');
        $method = $request->input('method');
        $currency = $request->input('currency');
        $search = trim($request->input('search', ''));

        $items = collect();

        if (!$type || $type === 'income') {

            $payments = Payment::query()
                ->with([
                    'invoice.student',
                    'user',
                ])
                ->where('status', 'confirmed')
                ->whereDate('payment_date', '>=', $dateFrom)
                ->whereDate('payment_date', '<=', $dateTo)
                ->when($method, fn ($q) =>
                    $q->where('payment_method', $method)
                )
                ->when($currency, fn ($q) =>
                    $q->where('currency', $currency)
                )
                ->get();

            foreach ($payments as $payment) {

                $student = $payment->invoice?->student;

                $items->push([
                    'date' => $payment->payment_date,
                    'type' => 'income',
                    'document' => $payment->payment_number ?? $payment->id,
                    'description' => 'Recebimento de estudante',
                    'party' => $student?->name
                        ?? $student?->nome
                        ?? '—',
                    'method' => $payment->payment_method ?? '—',
                    'reference' => $payment->transaction_reference ?? '—',
                    'currency' => strtoupper($payment->currency ?? 'AOA'),
                    'amount' => (float) $payment->amount,
                    'operator' => $payment->user?->name ?? '—',
                ]);
            }
        }

        if (!$type || $type === 'expense') {

            $payments = ExpensePayment::query()
                ->with([
                    'expense.supplier',
                    'user',
                ])
                ->where('status', 'confirmed')
                ->whereDate('payment_date', '>=', $dateFrom)
                ->whereDate('payment_date', '<=', $dateTo)
                ->when($method, fn ($q) =>
                    $q->where('payment_method', $method)
                )
                ->when($currency, fn ($q) =>
                    $q->where('currency', $currency)
                )
                ->get();

            foreach ($payments as $payment) {

                $supplier = $payment->expense?->supplier;

                $items->push([
                    'date' => $payment->payment_date,
                    'type' => 'expense',
                    'document' => $payment->payment_number ?? $payment->id,
                    'description' => $payment->expense?->description
                        ?? 'Pagamento de despesa',
                    'party' => $supplier?->name ?? '—',
                    'method' => $payment->payment_method ?? '—',
                    'reference' => $payment->transaction_reference ?? '—',
                    'currency' => strtoupper($payment->currency ?? 'AOA'),
                    'amount' => (float) $payment->amount,
                    'operator' => $payment->user?->name ?? '—',
                ]);
            }
        }

        $items = $items
            ->sortByDesc(fn ($item) => strtotime((string) $item['date']))
            ->values();

        $summary = $items
            ->groupBy('currency')
            ->map(function (Collection $rows, $currency) {

                $income = $rows
                    ->where('type', 'income')
                    ->sum('amount');

                $expense = $rows
                    ->where('type', 'expense')
                    ->sum('amount');

                return [
                    'currency' => $currency,
                    'income' => $income,
                    'expense' => $expense,
                    'net' => $income - $expense,
                ];
            })
            ->sortKeys();

        return view(
            'university.finance.treasury.reports.cash-flow-print',
            compact(
                'items',
                'summary',
                'dateFrom',
                'dateTo'
            )
        );
    }
}
