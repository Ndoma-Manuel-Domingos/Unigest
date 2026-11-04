<!DOCTYPE html>
<html lang="pt">

<head>

    <meta charset="UTF-8">

    <title>
        Relatório Diário da Tesouraria
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 0;
            padding: 30px;
            font-size: 12px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #111827;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
        }

        .subtitle {
            color: #6b7280;
            margin-top: 5px;
        }

        .summary {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 25px;
        }

        .card {
            border: 1px solid #d1d5db;
            padding: 12px;
        }

        .card-title {
            color: #6b7280;
            font-size: 10px;
        }

        .card-value {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #d1d5db;
            padding: 7px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
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

        .section {
            margin-top: 25px;
            margin-bottom: 10px;
            font-size: 15px;
            font-weight: bold;
        }

        .signature {
            margin-top: 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
        }

        .signature-line {
            border-top: 1px solid #111827;
            padding-top: 8px;
            text-align: center;
        }

        @media print {

            body {
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }

        }

    </style>

</head>

<body>

    <div class="no-print" style="margin-bottom:20px;">

        <button onclick="window.print()" style="padding:8px 14px; cursor:pointer;">
            Imprimir
        </button>

    </div>

    <div class="header">

        <div>

            <div class="title">
                Relatório Diário da Tesouraria
            </div>

            <div class="subtitle">
                Data:
                {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
            </div>

        </div>

        <div style="text-align:right;">

            <strong>
                {{ auth()->user()->university?->name ?? 'Universidade' }}
            </strong>

            <div class="subtitle">
                Emitido em {{ now()->format('d/m/Y H:i') }}
            </div>

        </div>

    </div>

    <div class="summary">

        <div class="card">
            <div class="card-title">
                Saldo Inicial
            </div>

            <div class="card-value">
                {{ number_format($closure?->opening_balance ?? 0, 2, ',', '.') }}
            </div>
        </div>

        <div class="card">
            <div class="card-title">
                Entradas
            </div>

            <div class="card-value green">
                {{ number_format($income, 2, ',', '.') }}
            </div>
        </div>

        <div class="card">
            <div class="card-title">
                Saídas
            </div>

            <div class="card-value red">
                {{ number_format($expense, 2, ',', '.') }}
            </div>
        </div>

        <div class="card">
            <div class="card-title">
                Saldo Esperado
            </div>

            <div class="card-value">
                {{ number_format($closure?->expected_balance ?? 0, 2, ',', '.') }}
            </div>
        </div>

        <div class="card">
            <div class="card-title">
                Diferença
            </div>

            <div class="card-value
                {{ ($closure?->difference ?? 0) == 0 ? 'green' : 'red' }}">

                {{ number_format($closure?->difference ?? 0, 2, ',', '.') }}

            </div>
        </div>

    </div>

    <div class="section">
        Movimentos do Dia
    </div>

    <table>

        <thead>

            <tr>
                <th>Hora</th>
                <th>Tipo</th>
                <th>Descrição</th>
                <th>Operador</th>
                <th class="right">Valor</th>
            </tr>

        </thead>

        <tbody>

            @forelse($movements as $movement)

            <tr>

                <td>
                    {{ \Carbon\Carbon::parse($movement->movement_date)->format('H:i') }}
                </td>

                <td>
                    {{ $movement->type === 'income' ? 'Entrada' : 'Saída' }}
                </td>

                <td>
                    {{ $movement->description }}
                </td>

                <td>
                    {{ $movement->user?->name ?? '—' }}
                </td>

                <td class="right
                        {{ $movement->type === 'income' ? 'green' : 'red' }}">

                    {{ number_format($movement->amount, 2, ',', '.') }}

                </td>

            </tr>

            @empty

            <tr>

                <td colspan="5" style="text-align:center;">
                    Nenhum movimento registado.
                </td>

            </tr>

            @endforelse

        </tbody>

    </table>

    @if($closure)

    <div class="section">
        Conferência do Fecho
    </div>

    <table>

        <tr>
            <th>Saldo Inicial</th>
            <td class="right">
                {{ number_format($closure->opening_balance, 2, ',', '.') }}
            </td>
        </tr>

        <tr>
            <th>Total de Entradas</th>
            <td class="right green">
                {{ number_format($closure->total_income, 2, ',', '.') }}
            </td>
        </tr>

        <tr>
            <th>Total de Saídas</th>
            <td class="right red">
                {{ number_format($closure->total_expense, 2, ',', '.') }}
            </td>
        </tr>

        <tr>
            <th>Saldo Esperado</th>
            <td class="right">
                {{ number_format($closure->expected_balance, 2, ',', '.') }}
            </td>
        </tr>

        <tr>
            <th>Saldo Contado</th>
            <td class="right">
                {{ number_format($closure->counted_balance, 2, ',', '.') }}
            </td>
        </tr>

        <tr>
            <th>Diferença</th>
            <td class="right
                    {{ $closure->difference == 0 ? 'green' : 'red' }}">

                {{ number_format($closure->difference, 2, ',', '.') }}

            </td>
        </tr>

    </table>

    @if($closure->notes)

    <div class="section">
        Observações
    </div>

    <div>
        {{ $closure->notes }}
    </div>

    @endif

    <div class="signature">

        <div class="signature-line">
            Responsável pela Tesouraria
        </div>

        <div class="signature-line">
            Conferência / Direcção
        </div>

    </div>

    @endif

</body>

</html>
