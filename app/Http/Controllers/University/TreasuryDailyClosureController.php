<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\TreasuryDailyClosure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TreasuryDailyClosureController extends Controller
{
    public function index()
    {
        $closures = TreasuryDailyClosure::query()
            ->with('closedBy')
            ->latest('closure_date')
            ->paginate(20);

        return view(
            'university.finance.treasury.daily-closures.index',
            compact('closures')
        );
    }

    public function create(Request $request)
    {
        $date = $request->input(
            'date',
            now()->toDateString()
        );

        $existing = TreasuryDailyClosure::query()
            ->whereDate('closure_date', $date)
            ->first();

        if ($existing) {
            return redirect()
                ->route(
                    'university.treasury.daily-closures.show',
                    $existing
                )
                ->with('info', 'Já existe um fecho para esta data.');
        }

        $totals = $this->calculateTotals($date);

        $previous = TreasuryDailyClosure::query()
            ->where('status', 'closed')
            ->whereDate('closure_date', '<', $date)
            ->latest('closure_date')
            ->first();

        $openingBalance = $previous
            ? (float) $previous->expected_balance
            : 0;

        $expectedBalance =
            $openingBalance
            + $totals['income']
            - $totals['expense'];

        return view(
            'university.finance.treasury.daily-closures.create',
            [
                'date' => $date,
                'openingBalance' => $openingBalance,
                'income' => $totals['income'],
                'expense' => $totals['expense'],
                'expectedBalance' => $expectedBalance,
                'movementsCount' => $totals['count'],
            ]
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'closure_date' => [
                'required',
                'date',
            ],

            'opening_balance' => [
                'required',
                'numeric',
                'min:0',
            ],

            'counted_balance' => [
                'required',
                'numeric',
                'min:0',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        return DB::transaction(function () use ($validated) {

            $exists = TreasuryDailyClosure::query()
                ->whereDate(
                    'closure_date',
                    $validated['closure_date']
                )
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'closure_date' =>
                            'Já existe um fecho para esta data.',
                    ]);
            }

            $totals = $this->calculateTotals(
                $validated['closure_date']
            );

            $opening = (float) $validated['opening_balance'];

            $expected =
                $opening
                + $totals['income']
                - $totals['expense'];

            $counted = (float) $validated['counted_balance'];

            $difference = $counted - $expected;

            $closure = TreasuryDailyClosure::create([
                'closure_date' => $validated['closure_date'],
                'opening_balance' => $opening,
                'total_income' => $totals['income'],
                'total_expense' => $totals['expense'],
                'expected_balance' => $expected,
                'counted_balance' => $counted,
                'difference' => $difference,
                'status' => 'closed',
                'closed_by' => auth()->id(),
                'closed_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()
                ->route(
                    'university.treasury.daily-closures.show',
                    $closure
                )
                ->with(
                    'success',
                    'Fecho diário realizado com sucesso.'
                );
        });
    }

    public function show(TreasuryDailyClosure $closure)
    {
        $closure->load('closedBy');

        return view(
            'university.finance.treasury.daily-closures.show',
            compact('closure')
        );
    }

    private function calculateTotals(string $date): array
    {
        $movements = CashMovement::query()
            ->whereDate('movement_date', $date)
            ->get();

        $income = 0;
        $expense = 0;

        foreach ($movements as $movement) {
            if ($movement->type === 'income') {
                $income += (float) $movement->amount;
            }

            if ($movement->type === 'expense') {
                $expense += (float) $movement->amount;
            }
        }

        return [
            'income' => $income,
            'expense' => $expense,
            'count' => $movements->count(),
        ];
    }
}
