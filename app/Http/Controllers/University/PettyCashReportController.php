<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\PettyCashFund;
use App\Models\PettyCashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PettyCashReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'fund_id' => [
                'nullable',
                'integer',
                Rule::exists('petty_cash_funds', 'id')
                    ->where('tenant_id', auth()->user()->tenant_id),
            ],
            'type' => ['nullable', 'in:expense,replenishment,adjustment'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $funds = PettyCashFund::query()
            ->orderBy('name')
            ->get();

        $query = PettyCashMovement::query()
            ->with(['fund', 'user', 'approver'])
            ->where('status', 'approved');

        if (!empty($filters['fund_id'])) {
            $query->where('petty_cash_fund_id', $filters['fund_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('movement_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('movement_date', '<=', $filters['date_to']);
        }

        $movements = (clone $query)
            ->orderByDesc('movement_date')
            ->orderByDesc('id')
            ->paginate(30)
            ->withQueryString();

        $totals = (clone $query)
            ->join('petty_cash_funds', 'petty_cash_funds.id', '=', 'petty_cash_movements.petty_cash_fund_id')
            ->select(
                'petty_cash_funds.currency',
                DB::raw("SUM(CASE WHEN petty_cash_movements.type = 'expense' THEN petty_cash_movements.amount ELSE 0 END) as expenses"),
                DB::raw("SUM(CASE WHEN petty_cash_movements.type = 'replenishment' THEN petty_cash_movements.amount ELSE 0 END) as replenishments"),
                DB::raw("SUM(CASE WHEN petty_cash_movements.type = 'adjustment' THEN petty_cash_movements.amount ELSE 0 END) as adjustments")
            )
            ->groupBy('petty_cash_funds.currency')
            ->get();

        $fundBalances = PettyCashFund::query()
            ->with(['responsible', 'department'])
            ->when(
                !empty($filters['fund_id']),
                fn ($q) => $q->whereKey($filters['fund_id'])
            )
            ->orderBy('name')
            ->get();

        return view('university.finance.petty-cash.reports.index', compact(
            'funds',
            'movements',
            'totals',
            'fundBalances',
            'filters'
        ));
    }

    public function exportCsv(Request $request)
    {
        $filters = $request->validate([
            'fund_id' => [
                'nullable',
                'integer',
                Rule::exists('petty_cash_funds', 'id')
                    ->where('tenant_id', auth()->user()->tenant_id),
            ],
            'type' => ['nullable', 'in:expense,replenishment,adjustment'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $query = $this->filteredMovements($filters);

        $filename = 'relatorio-caixa-pequena-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $output = fopen('php://output', 'w');

            // BOM para compatibilidade com Excel.
            fwrite($output, "\xEF\xBB\xBF");

            fputcsv($output, [
                'Referência',
                'Data',
                'Fundo',
                'Moeda',
                'Tipo',
                'Descrição',
                'Fornecedor',
                'Documento',
                'Utilizador',
                'Aprovado por',
                'Valor',
            ], ';');

            $query->with(['fund', 'user', 'approver'])
                ->orderBy('movement_date')
                ->chunk(500, function ($movements) use ($output) {
                    foreach ($movements as $movement) {
                        fputcsv($output, [
                            $movement->movement_number,
                            $movement->movement_date?->format('d/m/Y'),
                            $movement->fund?->name,
                            $movement->fund?->currency,
                            $movement->type,
                            $movement->description,
                            $movement->supplier_name,
                            $movement->document_number,
                            $movement->user?->name,
                            $movement->approver?->name,
                            $movement->amount,
                        ], ';');
                    }
                });

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function print(Request $request)
    {
        $filters = $request->validate([
            'fund_id' => [
                'nullable',
                'integer',
                Rule::exists('petty_cash_funds', 'id')
                    ->where('tenant_id', auth()->user()->tenant_id),
            ],
            'type' => ['nullable', 'in:expense,replenishment,adjustment'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $movements = $this->filteredMovements($filters)
            ->with(['fund', 'user', 'approver'])
            ->orderBy('movement_date')
            ->get();

        $totals = $movements
            ->groupBy(fn ($movement) => $movement->fund?->currency ?? 'N/A')
            ->map(function ($items) {
                return [
                    'expenses' => $items->where('type', 'expense')->sum(fn ($item) => (float) $item->amount),
                    'replenishments' => $items->where('type', 'replenishment')->sum(fn ($item) => (float) $item->amount),
                    'adjustments' => $items->where('type', 'adjustment')->sum(fn ($item) => (float) $item->amount),
                ];
            });

        return view('university.finance.petty-cash.reports.print', compact(
            'movements',
            'totals',
            'filters'
        ));
    }

    private function filteredMovements(array $filters)
    {
        return PettyCashMovement::query()
            ->where('status', 'approved')
            ->when(
                !empty($filters['fund_id']),
                fn ($q) => $q->where('petty_cash_fund_id', $filters['fund_id'])
            )
            ->when(
                !empty($filters['type']),
                fn ($q) => $q->where('type', $filters['type'])
            )
            ->when(
                !empty($filters['date_from']),
                fn ($q) => $q->whereDate('movement_date', '>=', $filters['date_from'])
            )
            ->when(
                !empty($filters['date_to']),
                fn ($q) => $q->whereDate('movement_date', '<=', $filters['date_to'])
            );
    }
}
