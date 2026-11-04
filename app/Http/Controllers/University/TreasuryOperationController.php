<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\CashRegister;
use App\Models\Expense;
use App\Models\Invoice;

class TreasuryOperationController extends Controller
{
    public function index()
    {
        $tenantId = auth()->user()->tenant_id;

        abort_unless($tenantId, 403, 'Universidade não identificada.');

        $openCashCount = CashRegister::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->count();

        $pendingInvoicesCount = Invoice::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['issued', 'partially_paid'])
            ->where('balance', '>', 0)
            ->count();

        $pendingExpensesCount = Expense::query()
            ->where('tenant_id', $tenantId)
            ->whereIn('status', ['approved', 'partially_paid'])
            ->where('balance', '>', 0)
            ->count();

        return view('university.finance.treasury.operations.index', compact(
            'openCashCount',
            'pendingInvoicesCount',
            'pendingExpensesCount'
        ));
    }
}
