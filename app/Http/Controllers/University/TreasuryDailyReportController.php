<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\TreasuryDailyClosure;
use Illuminate\Http\Request;

class TreasuryDailyReportController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input(
            'date',
            now()->toDateString()
        );

        $closure = TreasuryDailyClosure::query()
            ->with('closedBy')
            ->whereDate('closure_date', $date)
            ->first();

        $movements = CashMovement::query()
            ->with([
                'user',
                'cashRegister',
            ])
            ->whereDate('movement_date', $date)
            ->orderBy('movement_date')
            ->orderBy('id')
            ->get();

        $income = $movements
            ->where('type', 'income')
            ->sum('amount');

        $expense = $movements
            ->where('type', 'expense')
            ->sum('amount');

        return view(
            'university.finance.treasury.reports.daily',
            compact(
                'date',
                'closure',
                'movements',
                'income',
                'expense'
            )
        );
    }

    public function print(Request $request)
    {
        $date = $request->input(
            'date',
            now()->toDateString()
        );

        $closure = TreasuryDailyClosure::query()
            ->with('closedBy')
            ->whereDate('closure_date', $date)
            ->first();

        $movements = CashMovement::query()
            ->with([
                'user',
                'cashRegister',
            ])
            ->whereDate('movement_date', $date)
            ->orderBy('movement_date')
            ->orderBy('id')
            ->get();

        $income = $movements
            ->where('type', 'income')
            ->sum('amount');

        $expense = $movements
            ->where('type', 'expense')
            ->sum('amount');

        return view(
            'university.finance.treasury.reports.daily-print',
            compact(
                'date',
                'closure',
                'movements',
                'income',
                'expense'
            )
        );
    }
}
