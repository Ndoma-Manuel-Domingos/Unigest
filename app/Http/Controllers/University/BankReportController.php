<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BankReportController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $filters = $request->validate([
            'bank_account_id' => ['nullable', 'integer'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $dateFrom = $filters['date_from'] ?? now()->startOfMonth()->toDateString();
        $dateTo = $filters['date_to'] ?? now()->toDateString();
        $accountId = $filters['bank_account_id'] ?? null;

        $accounts = DB::table('bank_accounts')
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        // Movimentos bancários agrupados por moeda.
        $movementQuery = DB::table('bank_movements as m')
            ->join('bank_accounts as a', function ($join) use ($tenantId) {
                $join->on('a.id', '=', 'm.bank_account_id')
                    ->where('a.tenant_id', $tenantId);
            })
            ->where('m.tenant_id', $tenantId)
            ->whereBetween('m.movement_date', [$dateFrom, $dateTo]);

        if ($accountId) {
            $movementQuery->where('m.bank_account_id', $accountId);
        }

        $movementSummary = $movementQuery
            ->select('a.currency')
            ->selectRaw("
                COUNT(*) AS total_movements,
                COALESCE(SUM(CASE WHEN m.type = 'income' THEN m.amount ELSE 0 END), 0) AS income,
                COALESCE(SUM(CASE WHEN m.type = 'expense' THEN m.amount ELSE 0 END), 0) AS expenses,
                COALESCE(SUM(CASE WHEN m.type IN ('transfer_in', 'transfer_out') THEN m.amount ELSE 0 END), 0) AS transfers,
                COALESCE(SUM(CASE WHEN m.type = 'adjustment' THEN m.amount ELSE 0 END), 0) AS adjustments
            ")
            ->groupBy('a.currency')
            ->orderBy('a.currency')
            ->get();

        // Transacções recebidas da API bancária, também por moeda.
        $transactionQuery = DB::table('bank_api_transactions')
            ->where('tenant_id', $tenantId)
            ->whereBetween('occurred_at', [
                $dateFrom . ' 00:00:00',
                $dateTo . ' 23:59:59',
            ]);

        if ($accountId) {
            $transactionQuery->where('bank_account_id', $accountId);
        }

        $transactionSummary = $transactionQuery
            ->select('currency')
            ->selectRaw("
                COUNT(*) AS total,
                COUNT(*) FILTER (WHERE status = 'confirmed') AS confirmed,
                COUNT(*) FILTER (WHERE status = 'unmatched') AS unmatched,
                COUNT(*) FILTER (WHERE status = 'failed') AS failed,
                COALESCE(SUM(CASE WHEN status = 'confirmed' AND type = 'income' THEN amount ELSE 0 END), 0) AS confirmed_income
            ")
            ->groupBy('currency')
            ->orderBy('currency')
            ->get();

        // Reconciliações no período.
        $reconciliationsQuery = DB::table('bank_reconciliations')
            ->where('tenant_id', $tenantId)
            ->whereBetween('period_start', [$dateFrom, $dateTo]);

        if ($accountId) {
            $reconciliationsQuery->where('bank_account_id', $accountId);
        }

        $reconciliationSummary = $reconciliationsQuery
            ->selectRaw("
                COUNT(*) AS total,
                COUNT(*) FILTER (WHERE status = 'reconciled') AS reconciled,
                COUNT(*) FILTER (WHERE status = 'draft') AS draft
            ")
            ->first();

        // Últimos movimentos do período.
        $recentMovementsQuery = DB::table('bank_movements as m')
            ->join('bank_accounts as a', function ($join) use ($tenantId) {
                $join->on('a.id', '=', 'm.bank_account_id')
                    ->where('a.tenant_id', $tenantId);
            })
            ->where('m.tenant_id', $tenantId)
            ->whereBetween('m.movement_date', [$dateFrom, $dateTo])
            ->select([
                'm.id',
                'm.type',
                'm.reference',
                'm.description',
                'm.amount',
                'm.movement_date',
                'm.reconciled',
                'a.name as bank_account_name',
                'a.currency',
            ]);

        if ($accountId) {
            $recentMovementsQuery->where('m.bank_account_id', $accountId);
        }

        $recentMovements = $recentMovementsQuery
            ->orderByDesc('m.movement_date')
            ->orderByDesc('m.id')
            ->limit(20)
            ->get();

        return view('university.finance.bank-reports.index', compact(
            'accounts',
            'dateFrom',
            'dateTo',
            'accountId',
            'movementSummary',
            'transactionSummary',
            'reconciliationSummary',
            'recentMovements'
        ));
    }



    public function exportExcel(Request $request)
    {
        $data = $this->getExportData($request);

        $spreadsheet = new Spreadsheet();

        // Folha 1: Resumo
        $summarySheet = $spreadsheet->getActiveSheet();
        $summarySheet->setTitle('Resumo');

        $summarySheet->fromArray([
            ['RELATÓRIO FINANCEIRO BANCÁRIO'],
            ['Período', $data['dateFrom'], 'até', $data['dateTo']],
            ['Conta', $data['accountName']],
            [],
            [
                'Moeda',
                'Total de movimentos',
                'Entradas',
                'Saídas',
                'Transferências',
                'Ajustamentos',
            ],
        ], null, 'A1');

        $row = 6;

        foreach ($data['movementSummary'] as $item) {
            $summarySheet->fromArray([
                [
                    $item->currency,
                    (int) $item->total_movements,
                    (float) $item->income,
                    (float) $item->expenses,
                    (float) $item->transfers,
                    (float) $item->adjustments,
                ],
            ], null, 'A' . $row);

            $row++;
        }

        $summarySheet->getStyle('A1:F1')->getFont()->setBold(true)->setSize(16);
        $summarySheet->getStyle('A5:F5')->getFont()->setBold(true);
        $summarySheet->getStyle('A5:F5')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE5E7EB');

        foreach (range('A', 'F') as $column) {
            $summarySheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Folha 2: Movimentos detalhados
        $movementSheet = $spreadsheet->createSheet();
        $movementSheet->setTitle('Movimentos');

        $movementSheet->fromArray([
            [
                'Data',
                'Conta bancária',
                'Referência',
                'Descrição',
                'Tipo',
                'Valor',
                'Moeda',
                'Conciliado',
            ],
        ], null, 'A1');

        $row = 2;

        foreach ($data['movements'] as $movement) {
            $movementSheet->fromArray([
                [
                    $movement->movement_date,
                    $movement->bank_account_name,
                    $movement->reference,
                    $movement->description,
                    $this->movementTypeLabel($movement->type),
                    (float) $movement->amount,
                    $movement->currency,
                    $movement->reconciled ? 'Sim' : 'Não',
                ],
            ], null, 'A' . $row);

            $row++;
        }

        $movementSheet->getStyle('A1:H1')->getFont()->setBold(true);
        $movementSheet->getStyle('A1:H1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFE5E7EB');

        foreach (range('A', 'H') as $column) {
            $movementSheet->getColumnDimension($column)->setAutoSize(true);
        }


        $apiSheet = $spreadsheet->createSheet();
        $apiSheet->setTitle('Transações API');

        $apiSheet->fromArray([
            [
                'Data',
                'Conta',
                'ID Externo',
                'Referência',
                'Descrição',
                'Tipo',
                'Valor',
                'Moeda',
                'Estado',
            ],
        ], null, 'A1');

        $row = 2;

        foreach ($data['apiTransactions'] as $item) {
            $apiSheet->fromArray([
                [
                    $item->occurred_at,
                    $item->bank_account_name,
                    $item->external_transaction_id,
                    $item->reference,
                    $item->description,
                    $item->type,
                    (float) $item->amount,
                    $item->currency,
                    $item->status,
                ],
            ], null, 'A' . $row);

            $row++;
        }

        $apiSheet->getStyle('A1:I1')->getFont()->setBold(true);

        foreach (range('A', 'I') as $column) {
            $apiSheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = 'relatorio-bancario-' . now()->format('Ymd-His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getExportData($request);

        $pdf = Pdf::loadView(
            'university.finance.bank-reports.pdf',
            $data
        )->setPaper('a4', 'landscape');

        return $pdf->download(
            'relatorio-bancario-' . now()->format('Ymd-His') . '.pdf'
        );
    }

    private function getExportData(Request $request): array
    {
        $filters = $request->validate([
            'bank_account_id' => ['nullable', 'integer'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $tenantId = auth()->user()->tenant_id;

        $dateFrom = $filters['date_from']
            ?? now()->startOfMonth()->toDateString();

        $dateTo = $filters['date_to']
            ?? now()->toDateString();

        $accountId = $filters['bank_account_id'] ?? null;

        $accountName = 'Todas as contas';

        if ($accountId) {
            $account = DB::table('bank_accounts')
                ->where('tenant_id', $tenantId)
                ->where('id', $accountId)
                ->first();

            abort_if(!$account, 404);

            $accountName = $account->name;
        }

        $baseQuery = DB::table('bank_movements as m')
            ->join('bank_accounts as a', function ($join) use ($tenantId) {
                $join->on('a.id', '=', 'm.bank_account_id')
                    ->where('a.tenant_id', $tenantId);
            })
            ->where('m.tenant_id', $tenantId)
            ->whereBetween('m.movement_date', [$dateFrom, $dateTo]);

        if ($accountId) {
            $baseQuery->where('m.bank_account_id', $accountId);
        }

        $movementSummary = (clone $baseQuery)
            ->select('a.currency')
            ->selectRaw("
                COUNT(*) AS total_movements,
                COALESCE(SUM(CASE WHEN m.type = 'income' THEN m.amount ELSE 0 END), 0) AS income,
                COALESCE(SUM(CASE WHEN m.type = 'expense' THEN m.amount ELSE 0 END), 0) AS expenses,
                COALESCE(SUM(CASE WHEN m.type IN ('transfer_in', 'transfer_out') THEN m.amount ELSE 0 END), 0) AS transfers,
                COALESCE(SUM(CASE WHEN m.type = 'adjustment' THEN m.amount ELSE 0 END), 0) AS adjustments
            ")
            ->groupBy('a.currency')
            ->orderBy('a.currency')
            ->get();

        $movements = (clone $baseQuery)
            ->select([
                'm.movement_date',
                'm.reference',
                'm.description',
                'm.type',
                'm.amount',
                'm.reconciled',
                'a.name as bank_account_name',
                'a.currency',
            ])
            ->orderBy('m.movement_date')
            ->orderBy('m.id')
            ->get();

        $apiQuery = DB::table('bank_api_transactions as t')
            ->join('bank_accounts as a', function ($join) use ($tenantId) {
                $join->on('a.id', '=', 't.bank_account_id')
                    ->where('a.tenant_id', $tenantId);
            })
            ->where('t.tenant_id', $tenantId)
            ->whereBetween('t.occurred_at', [
                $dateFrom . ' 00:00:00',
                $dateTo . ' 23:59:59',
            ]);

        if ($accountId) {
            $apiQuery->where('t.bank_account_id', $accountId);
        }

        $apiTransactions = $apiQuery
            ->select([
                't.occurred_at',
                't.external_transaction_id',
                't.reference',
                't.description',
                't.type',
                't.amount',
                't.currency',
                't.status',
                'a.name as bank_account_name',
            ])
            ->orderBy('t.occurred_at')
            ->get();

        return compact(
            'dateFrom',
            'dateTo',
            'accountName',
            'movementSummary',
            'movements',
            'apiTransactions'
        );
    }

    private function movementTypeLabel(string $type): string
    {
        return [
            'income' => 'Entrada',
            'expense' => 'Saída',
            'transfer_in' => 'Transferência recebida',
            'transfer_out' => 'Transferência enviada',
            'adjustment' => 'Ajustamento',
        ][$type] ?? $type;
    }

}
