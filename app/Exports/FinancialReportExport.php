<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FinancialReportExport implements FromArray, WithHeadings, WithStyles
{
    public function __construct(
        private array $rows
    ) {}

    public function array(): array
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'Data',
            'Tipo',
            'Número',
            'Descrição',
            'Método de pagamento',
            'Valor',
            'Moeda',
            'Estado',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '1F2937'],
                ],
            ],
        ];
    }
}