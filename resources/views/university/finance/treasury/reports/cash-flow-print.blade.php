<!DOCTYPE html>
<html lang="pt">

<head>

    <meta charset="UTF-8">

    <title>
        Mapa de Recebimentos e Pagamentos
    </title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111827;
            font-size: 11px;
            padding: 25px;
        }

        h1 {
            margin: 0;
            font-size: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #111827;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .muted {
            color: #6b7280;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }

        .summary-card {
            border: 1px solid #d1d5db;
            padding: 12px;
        }

        .summary-card strong {
            display: block;
            margin-top: 5px;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 6px;
        }

        th {
            background: #f3f4f6;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        .green {
            color: #15803d;
        }

        .red {
            color: #b91c1c;
        }

        .no-print {
            margin-bottom: 20px;
        }

        @media print {

            .no-print {
                display: none;
            }

            body {
                padding: 5px;
            }

        }

    </style>

</head>

<body>

    <div class="no-print">

        <button onclick="window.print()" style="padding:8px 14px; cursor:pointer;">
            Imprimir
        </button>

    </div>

    <div class="header">

        <div>

            <h1>
                Mapa de Recebimentos e Pagamentos
            </h1>

            <div class="muted">
                Período:
                {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
                —
                {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
            </div>

        </div>

        <div style="text-align:right">

            <strong>
                {{ auth()->user()->university?->name ?? 'Universidade' }}
            </strong>

            <div class="muted">
                Emitido em {{ now()->format('d/m/Y H:i') }}
            </div>

        </div>

    </div>

    <div class="summary">

        @foreach($summary as $data)

        <div class="summary-card">

            <strong>
                {{ $data['currency'] }}
            </strong>

            <div class="green">
                Entradas:
                {{ number_format($data['income'], 2, ',', '.') }}
            </div>

            <div class="red">
                Saídas:
                {{ number_format($data['expense'], 2, ',', '.') }}
            </div>

            <div>
                Líquido:
                {{ number_format($data['net'], 2, ',', '.') }}
            </div>

        </div>

        @endforeach

    </div>

    <table>

        <thead>

            <tr>

                <th>Data</th>
                <th>Tipo</th>
                <th>Documento</th>
                <th>Descrição</th>
                <th>Beneficiário</th>
                <th>Método</th>
                <th>Operador</th>
                <th class="right">Valor</th>

            </tr>

        </thead>

        <tbody>

            @forelse($items as $item)

            <tr>

                <td>
                    {{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y H:i') }}
                </td>

                <td>
                    {{ $item['type'] === 'income'
                            ? 'Recebimento'
                            : 'Pagamento' }}
                </td>

                <td>
                    {{ $item['document'] }}
                </td>

                <td>
                    {{ $item['description'] }}
                </td>

                <td>
                    {{ $item['party'] }}
                </td>

                <td>
                    {{ ucfirst(str_replace('_', ' ', $item['method'])) }}
                </td>

                <td>
                    {{ $item['operator'] }}
                </td>

                <td class="right
                        {{ $item['type'] === 'income'
                            ? 'green'
                            : 'red' }}">

                    {{ $item['type'] === 'income' ? '+' : '-' }}

                    {{ number_format($item['amount'], 2, ',', '.') }}

                    {{ $item['currency'] }}

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="8" style="text-align:center">
                    Nenhum movimento encontrado.
                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

</body>

</html>
