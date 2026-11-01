<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\ExpensePayment;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $dateFrom = Carbon::parse(
            $validated['date_from'] ?? now()->startOfMonth()->toDateString()
        )->startOfDay();

        $dateTo = Carbon::parse(
            $validated['date_to'] ?? now()->toDateString()
        )->endOfDay();

        /*
        |--------------------------------------------------------------------------
        | Receitas recebidas
        |--------------------------------------------------------------------------
        */

        $paymentsQuery = Payment::query()
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$dateFrom, $dateTo]);

        $totalRevenue = (clone $paymentsQuery)->sum('amount');

        $revenueByMethod = (clone $paymentsQuery)
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        $paymentsCount = (clone $paymentsQuery)->count();

        /*
        |--------------------------------------------------------------------------
        | Despesas pagas
        |--------------------------------------------------------------------------
        */

        $expensePaymentsQuery = ExpensePayment::query()
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$dateFrom, $dateTo]);

        $totalExpensesPaid = (clone $expensePaymentsQuery)->sum('amount');

        $expensesByMethod = (clone $expensePaymentsQuery)
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        $expensePaymentsCount = (clone $expensePaymentsQuery)->count();

        /*
        |--------------------------------------------------------------------------
        | Movimentos de caixa
        |--------------------------------------------------------------------------
        */

        $cashMovementsQuery = CashMovement::query()
            ->whereBetween('movement_date', [$dateFrom, $dateTo]);

        $cashIncome = (clone $cashMovementsQuery)
            ->where('type', 'income')
            ->sum('amount');

        $cashExpenses = (clone $cashMovementsQuery)
            ->where('type', 'expense')
            ->sum('amount');

        $cashMovements = (clone $cashMovementsQuery)
            ->with(['user', 'cashRegister'])
            ->orderByDesc('movement_date')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Resultado financeiro
        |--------------------------------------------------------------------------
        */

        $financialBalance = (float) $totalRevenue - (float) $totalExpensesPaid;

        return view('university.finance.reports.index', compact(
            'dateFrom',
            'dateTo',
            'totalRevenue',
            'revenueByMethod',
            'paymentsCount',
            'totalExpensesPaid',
            'expensesByMethod',
            'expensePaymentsCount',
            'cashIncome',
            'cashExpenses',
            'cashMovements',
            'financialBalance'
        ));
    }
}
