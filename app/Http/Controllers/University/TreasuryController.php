<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\CashRegister;
use App\Models\CashMovement;
use App\Models\Expense;
use App\Models\ExpensePayment;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TreasuryController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        abort_unless($tenantId, 403, 'Universidade não identificada.');

        $today = now()->toDateString();
        $monthStart = now()->startOfMonth()->toDateString();
        $monthEnd = now()->endOfMonth()->toDateString();

        // RECEBIMENTOS CONFIRMADOS
        $receiptsToday = Payment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'confirmed')
            ->whereDate('payment_date', $today)
            ->sum('amount');

        $receiptsMonth = Payment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$monthStart, $monthEnd])
            ->sum('amount');

        // PAGAMENTOS DE DESPESAS CONFIRMADOS
        $expensesToday = ExpensePayment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'confirmed')
            ->whereDate('payment_date', $today)
            ->sum('amount');

        $expensesMonth = ExpensePayment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$monthStart, $monthEnd])
            ->sum('amount');

        // CAIXAS ABERTAS
        $openCashRegisters = CashRegister::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->with('user')
            ->orderByDesc('opened_at')
            ->get();

        $cashAvailable = $openCashRegisters->sum('expected_balance');

        // SALDOS BANCÁRIOS
        $bankAccounts = BankAccount::query()
            ->where('tenant_id', $tenantId)
            ->where('is_active', true)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        $bankBalance = $bankAccounts->sum('current_balance');

        // FACTURAS POR LIQUIDAR
        $outstandingInvoices = Invoice::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['issued', 'partially_paid'])
            ->where('balance', '>', 0)
            ->sum('balance');

        $pendingInvoicesCount = Invoice::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['issued', 'partially_paid'])
            ->where('balance', '>', 0)
            ->count();

        // DESPESAS A PAGAR
        $pendingExpenses = Expense::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['approved', 'partially_paid'])
            ->where('balance', '>', 0)
            ->sum('balance');

        // ÚLTIMOS MOVIMENTOS DE CAIXA
        $recentMovements = CashMovement::query()
            ->where('tenant_id', $tenantId)
            ->with(['cashRegister', 'user'])
            ->orderByDesc('movement_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // RESUMO MENSAL DOS RECEBIMENTOS E PAGAMENTOS
        $monthlyReceipts = Payment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$monthStart, $monthEnd])
            ->sum('amount');

        $monthlyExpenses = ExpensePayment::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'confirmed')
            ->whereBetween('payment_date', [$monthStart, $monthEnd])
            ->sum('amount');

        return view('university.finance.treasury.index', compact(
            'receiptsToday',
            'receiptsMonth',
            'expensesToday',
            'expensesMonth',
            'openCashRegisters',
            'cashAvailable',
            'bankAccounts',
            'bankBalance',
            'outstandingInvoices',
            'pendingInvoicesCount',
            'pendingExpenses',
            'recentMovements',
            'monthlyReceipts',
            'monthlyExpenses'
        ));
    }

    public function receipt(string $payment)
    {
        $payment = Payment::findOrFail($payment);

        abort_unless(
            $payment->tenant_id === auth()->user()->tenant_id,
            404
        );

        $payment->load([
            'invoice.student',
            'invoice.items',
            'user',
        ]);

        abort_unless(
            $payment->status === 'confirmed',
            404,
            'Este pagamento ainda não está confirmado.'
        );

        return view(
            'university.finance.treasury.receipts.show',
            compact('payment')
        );
    }


}
