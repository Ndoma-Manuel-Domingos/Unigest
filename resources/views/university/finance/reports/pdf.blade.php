<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">

    <style>
        @page {
            margin: 25px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #333;
        }

        h1 {
            font-size: 19px;
            margin-bottom: 5px;
        }

        h2 {
            font-size: 13px;
            margin-top: 22px;
        }

        .muted {
            color: #666;
        }

        .summary {
            width: 100%;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .summary td {
            border: 1px solid #ddd;
            padding: 10px;
            width: 33.33%;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        table.data th,
        table.data td {
            border: 1px solid #ddd;
            padding: 7px;
            text-align: left;
        }

        table.data th {
            background: #eee;
        }

        .right {
            text-align: right !important;
        }

        .footer {
            margin-top: 25px;
            color: #777;
            font-size: 9px;
        }

    </style>
</head>

<body>

    <h1>Relatório Financeiro</h1>

    <p class="muted">
        Período:
        {{ $dateFrom->format('d/m/Y') }}
        até
        {{ $dateTo->format('d/m/Y') }}
    </p>

    <table class="summary">
        <tr>
            <td>
                <strong>Receitas recebidas</strong><br>
                {{ number_format($totalRevenue, 2, ',', '.') }} AOA
            </td>

            <td>
                <strong>Despesas pagas</strong><br>
                {{ number_format($totalExpensesPaid, 2, ',', '.') }} AOA
            </td>

            <td>
                <strong>Saldo do período</strong><br>
                {{ number_format($financialBalance, 2, ',', '.') }} AOA
            </td>
        </tr>
    </table>

    <h2>Receitas por método</h2>

    <table class="data">
        <thead>
            <tr>
                <th>Método</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($revenueByMethod as $item)
            <tr>
                <td>{{ $methodLabels[$item->payment_method] ?? $item->payment_method }}</td>
                <td class="right">{{ number_format($item->total, 2, ',', '.') }} AOA</td>
            </tr>
            @empty
            <tr>
                <td colspan="2">Sem receitas no período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Despesas por método</h2>

    <table class="data">
        <thead>
            <tr>
                <th>Método</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($expensesByMethod as $item)
            <tr>
                <td>{{ $methodLabels[$item->payment_method] ?? $item->payment_method }}</td>
                <td class="right">{{ number_format($item->total, 2, ',', '.') }} AOA</td>
            </tr>
            @empty
            <tr>
                <td colspan="2">Sem pagamentos de despesas no período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Movimentos de caixa</h2>

    <table class="data">
        <thead>
            <tr>
                <th>Data</th>
                <th>Descrição</th>
                <th>Tipo</th>
                <th>Utilizador</th>
                <th class="right">Valor</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($cashMovements as $movement)
            <tr>
                <td>{{ $movement->movement_date?->format('d/m/Y H:i') }}</td>
                <td>{{ $movement->description }}</td>
                <td>{{ $movement->type === 'income' ? 'Entrada' : 'Saída' }}</td>
                <td>{{ $movement->user?->name ?? '—' }}</td>
                <td class="right">
                    {{ number_format($movement->amount, 2, ',', '.') }} AOA
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5">Sem movimentos de caixa no período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <p class="footer">
        Documento gerado em {{ now()->format('d/m/Y H:i') }}.
    </p>

</body>
</html>
