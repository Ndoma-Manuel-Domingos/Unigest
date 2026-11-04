<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Caixa Pequena</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #222;
            font-size: 12px;
            margin: 25px;
        }

        h1 {
            font-size: 21px;
            margin-bottom: 5px;
        }

        h2 {
            font-size: 15px;
            margin-top: 25px;
        }

        .muted {
            color: #666;
        }

        .summary {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin: 20px 0;
        }

        .summary-card {
            border: 1px solid #ddd;
            padding: 12px;
            min-width: 160px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        .right {
            text-align: right;
        }

        .toolbar {
            margin-bottom: 20px;
        }

        @media print {
            body {
                margin: 10mm;
            }

            .toolbar {
                display: none;
            }

            tr {
                page-break-inside: avoid;
            }
        }

    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">Imprimir / Guardar PDF</button>
    </div>

    <h1>Relatório da Caixa Pequena</h1>
    <p class="muted">
        Emitido em {{ now()->format('d/m/Y H:i') }}
    </p>

    <p>
        <strong>Período:</strong>
        {{ $filters['date_from'] ?? 'Início não definido' }}
        até
        {{ $filters['date_to'] ?? 'Data actual' }}
    </p>

    <h2>Resumo por moeda</h2>

    @foreach($totals as $currency => $total)
    <div class="summary">
        <div class="summary-card">
            <strong>{{ $currency }}</strong>
            <p>Despesas: {{ number_format($total['expenses'], 2, ',', '.') }}</p>
            <p>Reposições: {{ number_format($total['replenishments'], 2, ',', '.') }}</p>
            <p>Ajustes: {{ number_format($total['adjustments'], 2, ',', '.') }}</p>
        </div>
    </div>
    @endforeach

    <h2>Movimentos aprovados</h2>

    <table>
        <thead>
            <tr>
                <th>Referência</th>
                <th>Data</th>
                <th>Fundo</th>
                <th>Descrição</th>
                <th>Tipo</th>
                <th class="right">Valor</th>
                <th>Utilizador</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $movement)
            <tr>
                <td>{{ $movement->movement_number }}</td>
                <td>{{ $movement->movement_date?->format('d/m/Y') }}</td>
                <td>{{ $movement->fund?->name }}</td>
                <td>{{ $movement->description }}</td>
                <td>
                    {{ [
                            'expense' => 'Despesa',
                            'replenishment' => 'Reposição',
                            'adjustment' => 'Abertura/Ajuste'
                        ][$movement->type] ?? $movement->type }}
                </td>
                <td class="right">
                    {{ number_format((float) $movement->amount, 2, ',', '.') }}
                    {{ $movement->fund?->currency }}
                </td>
                <td>{{ $movement->user?->name ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7">Sem movimentos para apresentar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
