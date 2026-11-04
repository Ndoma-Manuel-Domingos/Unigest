<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\ExpensePayment;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\FinancialReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

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

    private function getExportPeriod(Request $request): array
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

        return [$dateFrom, $dateTo];
    }

    public function exportExcel(Request $request)
    {
        [$dateFrom, $dateTo] = $this->getExportPeriod($request);

        $rows = [];

        $payments = Payment::query()
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->orderBy('payment_date')
            ->get();

        foreach ($payments as $payment) {
            $rows[] = [
                $payment->payment_date?->format('d/m/Y H:i'),
                'Receita',
                $payment->payment_number,
                'Pagamento recebido',
                $payment->payment_method,
                (float) $payment->amount,
                $payment->currency,
                'Confirmado',
            ];
        }

        $expensePayments = ExpensePayment::query()
            ->with('expense')
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$dateFrom, $dateTo])
            ->orderBy('payment_date')
            ->get();

        foreach ($expensePayments as $payment) {
            $rows[] = [
                $payment->payment_date?->format('d/m/Y H:i'),
                'Despesa',
                $payment->payment_number,
                'Pagamento da despesa ' . ($payment->expense?->expense_number ?? ''),
                $payment->payment_method,
                (float) $payment->amount,
                $payment->currency,
                'Confirmado',
            ];
        }

        $cashMovements = CashMovement::query()
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->orderBy('movement_date')
            ->get();

        foreach ($cashMovements as $movement) {
            $rows[] = [
                $movement->movement_date?->format('d/m/Y H:i'),
                'Movimento de caixa',
                $movement->id,
                $movement->description,
                $movement->source,
                (float) $movement->amount,
                'AOA',
                $movement->type === 'income' ? 'Entrada' : 'Saída',
            ];
        }

        return Excel::download(
            new FinancialReportExport($rows),
            'relatorio-financeiro-' . $dateFrom->format('Ymd') . '-' .
            $dateTo->format('Ymd') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        [$dateFrom, $dateTo] = $this->getExportPeriod($request);

        $paymentsQuery = Payment::query()
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$dateFrom, $dateTo]);

        $totalRevenue = (clone $paymentsQuery)->sum('amount');

        $revenueByMethod = (clone $paymentsQuery)
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        $expensePaymentsQuery = ExpensePayment::query()
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$dateFrom, $dateTo]);

        $totalExpensesPaid = (clone $expensePaymentsQuery)->sum('amount');

        $expensesByMethod = (clone $expensePaymentsQuery)
            ->selectRaw('payment_method, SUM(amount) as total')
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get();

        $cashMovements = CashMovement::query()
            ->with('user')
            ->whereBetween('movement_date', [$dateFrom, $dateTo])
            ->orderBy('movement_date')
            ->get();

        $financialBalance = (float) $totalRevenue - (float) $totalExpensesPaid;

        $methodLabels = [
            'cash' => 'Dinheiro',
            'bank_transfer' => 'Transferência bancária',
            'multicaixa' => 'Multicaixa',
            'pos' => 'POS',
            'reference' => 'Referência',
            'other' => 'Outro',
        ];

        $pdf = Pdf::loadView('university.finance.reports.pdf', compact(
            'dateFrom',
            'dateTo',
            'totalRevenue',
            'totalExpensesPaid',
            'financialBalance',
            'revenueByMethod',
            'expensesByMethod',
            'cashMovements',
            'methodLabels'
        ));

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download(
            'relatorio-financeiro-' . $dateFrom->format('Ymd') . '-' .
            $dateTo->format('Ymd') . '.pdf'
        );
    }
}
