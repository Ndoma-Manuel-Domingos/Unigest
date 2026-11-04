<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TreasuryOperatorController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = CashRegister::query()
            ->with('user')
            ->where('tenant_id', $tenantId);

        if ($request->filled('operator_id')) {
            $query->where('user_id', $request->operator_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('opened_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('opened_at', '<=', $request->date_to);
        }

        $registers = $query
            ->latest('opened_at')
            ->paginate(20)
            ->withQueryString();

        // Calcula os totais dos movimentos das caixas desta página.
        $registerIds = $registers->getCollection()->pluck('id');

        $totals = CashMovement::query()
            ->select('cash_register_id')
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END), 0) AS total_income
            ")
            ->selectRaw("
                COALESCE(SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END), 0) AS total_expense
            ")
            ->where('tenant_id', $tenantId)
            ->whereIn('cash_register_id', $registerIds)
            ->groupBy('cash_register_id')
            ->get()
            ->keyBy('cash_register_id');

        $registers->getCollection()->transform(function ($register) use ($totals) {
            $total = $totals->get($register->id);

            $register->report_income = (float) ($total->total_income ?? 0);
            $register->report_expense = (float) ($total->total_expense ?? 0);

            return $register;
        });

        $operators = User::query()
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('university.finance.treasury.operators.index', compact(
            'registers',
            'operators'
        ));
    }

    public function show(CashRegister $cashRegister)
    {
        abort_unless(
            $cashRegister->tenant_id === auth()->user()->tenant_id,
            404
        );

        $cashRegister->load('user');

        $movementsQuery = CashMovement::query()
            ->with('user')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('cash_register_id', $cashRegister->id);

        $totalIncome = (clone $movementsQuery)
            ->where('type', 'income')
            ->sum('amount');

        $totalExpense = (clone $movementsQuery)
            ->where('type', 'expense')
            ->sum('amount');

        $movements = $movementsQuery
            ->latest('movement_date')
            ->latest('id')
            ->paginate(30);

        $expectedBalance = (float) $cashRegister->opening_balance
            + (float) $totalIncome
            - (float) $totalExpense;

        return view('university.finance.treasury.operators.show', compact(
            'cashRegister',
            'movements',
            'totalIncome',
            'totalExpense',
            'expectedBalance'
        ));
    }
}
