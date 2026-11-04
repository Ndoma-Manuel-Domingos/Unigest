<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Relatório Bancário</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        h1 {
            font-size: 18px;
            margin-bottom: 5px;
        }

        h2 {
            font-size: 13px;
            margin-top: 25px;
        }

        .muted {
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 7px;
            text-align: left;
        }

        th {
            background: #eee;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .footer {
            margin-top: 25px;
            font-size: 9px;
            color: #777;
        }

    </style>
</head>

<body>

    <h1>Relatório Financeiro Bancário</h1>

    <p class="muted">
        Período: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
        até {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
    </p>

    <p>
        <strong>Conta bancária:</strong> {{ $accountName }}
    </p>

    <p>
        <strong>Emitido em:</strong> {{ now()->format('d/m/Y H:i') }}
    </p>

    <h2>Resumo por moeda</h2>

    <table>
        <thead>
            <tr>
                <th>Moeda</th>
                <th class="right">Movimentos</th>
                <th class="right">Entradas</th>
                <th class="right">Saídas</th>
                <th class="right">Transferências</th>
                <th class="right">Ajustamentos</th>
            </tr>
        </thead>

        <tbody>
            @forelse($movementSummary as $item)
            <tr>
                <td>{{ $item->currency }}</td>
                <td class="right">{{ $item->total_movements }}</td>
                <td class="right">{{ number_format($item->income, 2, ',', '.') }}</td>
                <td class="right">{{ number_format($item->expenses, 2, ',', '.') }}</td>
                <td class="right">{{ number_format($item->transfers, 2, ',', '.') }}</td>
                <td class="right">{{ number_format($item->adjustments, 2, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="center">Sem movimentos no período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Movimentos detalhados</h2>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Conta</th>
                <th>Referência</th>
                <th>Descrição</th>
                <th>Tipo</th>
                <th class="right">Valor</th>
                <th>Moeda</th>
                <th>Conciliado</th>
            </tr>
        </thead>

        <tbody>
            @forelse($movements as $movement)
            <tr>
                <td>{{ \Carbon\Carbon::parse($movement->movement_date)->format('d/m/Y') }}</td>
                <td>{{ $movement->bank_account_name }}</td>
                <td>{{ $movement->reference }}</td>
                <td>{{ $movement->description }}</td>
                <td>
                    {{ [
                            'income' => 'Entrada',
                            'expense' => 'Saída',
                            'transfer_in' => 'Transferência recebida',
                            'transfer_out' => 'Transferência enviada',
                            'adjustment' => 'Ajustamento',
                        ][$movement->type] ?? $movement->type }}
                </td>
                <td class="right">{{ number_format($movement->amount, 2, ',', '.') }}</td>
                <td>{{ $movement->currency }}</td>
                <td>{{ $movement->reconciled ? 'Sim' : 'Não' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="center">Sem movimentos no período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <h2>Transacções recebidas pelas integrações bancárias</h2>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Conta</th>
                <th>ID Externo</th>
                <th>Referência</th>
                <th>Tipo</th>
                <th class="right">Valor</th>
                <th>Moeda</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody>
            @forelse($apiTransactions as $item)
            <tr>
                <td>{{ \Carbon\Carbon::parse($item->occurred_at)->format('d/m/Y H:i') }}</td>
                <td>{{ $item->bank_account_name }}</td>
                <td>{{ $item->external_transaction_id }}</td>
                <td>{{ $item->reference }}</td>
                <td>{{ $item->type === 'income' ? 'Entrada' : 'Saída' }}</td>
                <td class="right">{{ number_format($item->amount, 2, ',', '.') }}</td>
                <td>{{ $item->currency }}</td>
                <td>{{ $item->status }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="center">Sem transacções recebidas neste período.</td>
            </tr>
            @endforelse
        </tbody>
    </table>


    <p class="footer">
        Documento gerado automaticamente pelo Sistema de Gestão Universitário.
    </p>

</body>
</html>
