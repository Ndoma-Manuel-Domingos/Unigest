<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">

    <title>
        Recibo {{ $payment->payment_number ?? $payment->id }}
    </title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 30px;
            background: #f3f4f6;
            font-family: Arial, Helvetica, sans-serif;
            color: #111827;
        }

        .actions {
            max-width: 850px;
            margin: 0 auto 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            border: 0;
            padding: 10px 16px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-print {
            background: #2563eb;
            color: white;
        }

        .btn-back {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .receipt {
            max-width: 850px;
            margin: 0 auto;
            background: white;
            padding: 45px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .08);
        }

        .header {
            display: flex;
            justify-content: space-between;
            gap: 30px;
            border-bottom: 2px solid #111827;
            padding-bottom: 20px;
        }

        .institution h1 {
            margin: 0 0 6px;
            font-size: 24px;
        }

        .institution p {
            margin: 3px 0;
            color: #6b7280;
            font-size: 13px;
        }

        .receipt-title {
            text-align: right;
        }

        .receipt-title h2 {
            margin: 0;
            font-size: 25px;
            text-transform: uppercase;
        }

        .receipt-title p {
            margin: 6px 0 0;
            font-size: 13px;
            color: #6b7280;
        }

        .section {
            margin-top: 30px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 12px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px 30px;
        }

        .info {
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .label {
            display: block;
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .value {
            font-size: 14px;
            font-weight: 500;
        }

        .amount-box {
            margin-top: 30px;
            border: 2px solid #111827;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .amount-label {
            font-size: 14px;
            font-weight: bold;
        }

        .amount {
            font-size: 25px;
            font-weight: bold;
        }

        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .items th,
        .items td {
            border-bottom: 1px solid #e5e7eb;
            padding: 10px 8px;
            text-align: left;
            font-size: 13px;
        }

        .items th {
            color: #6b7280;
            font-size: 11px;
            text-transform: uppercase;
        }

        .footer {
            margin-top: 50px;
            padding-top: 15px;
            border-top: 1px solid #d1d5db;
            display: flex;
            justify-content: space-between;
            gap: 20px;
        }

        .footer div {
            width: 45%;
            font-size: 11px;
            color: #6b7280;
        }

        .notice {
            margin-top: 25px;
            font-size: 11px;
            color: #6b7280;
            text-align: center;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .actions {
                display: none;
            }

            .receipt {
                max-width: none;
                margin: 0;
                box-shadow: none;
                padding: 20px;
            }
        }

    </style>
</head>

<body>

    <div class="actions">
        <a href="{{ url()->previous() }}" class="btn btn-back">
            Voltar
        </a>

        <button onclick="window.print()" class="btn btn-print">
            Imprimir recibo
        </button>
    </div>

    <div class="receipt">

        <div class="header">

            <div class="institution">
                <h1>
                    {{ auth()->user()->university?->name ?? 'Universidade' }}
                </h1>

                <p>
                    Tesouraria
                </p>

                <p>
                    Documento oficial de recebimento
                </p>
            </div>

            <div class="receipt-title">
                <h2>Recibo</h2>

                <p>
                    Nº
                    <strong>
                        {{ $payment->payment_number ?? $payment->id }}
                    </strong>
                </p>
            </div>

        </div>

        <div class="section">

            <div class="section-title">
                Dados do pagamento
            </div>

            <div class="info-grid">

                <div class="info">
                    <span class="label">Data do pagamento</span>

                    <span class="value">
                        {{ optional($payment->payment_date)->format('d/m/Y') }}
                    </span>
                </div>

                <div class="info">
                    <span class="label">Referência</span>

                    <span class="value">
                        {{ $payment->transaction_reference ?? '—' }}
                    </span>
                </div>

                <div class="info">
                    <span class="label">Método de pagamento</span>

                    <span class="value">
                        {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                    </span>
                </div>

                <div class="info">
                    <span class="label">Estado</span>

                    <span class="value">
                        Confirmado
                    </span>
                </div>

            </div>

        </div>

        <div class="section">

            <div class="section-title">
                Estudante
            </div>

            <div class="info-grid">

                <div class="info">
                    <span class="label">Nome</span>

                    <span class="value">
                        {{ $payment->invoice?->student?->name
                        ?? $payment->invoice?->student?->nome
                        ?? '—' }}
                    </span>
                </div>

                <div class="info">
                    <span class="label">Factura</span>

                    <span class="value">
                        {{ $payment->invoice?->invoice_number ?? '—' }}
                    </span>
                </div>

            </div>
        </div>

        @if($payment->invoice?->items?->count())

        <div class="section">

            <div class="section-title">
                Descrição
            </div>

            <table class="items">

                <thead>
                    <tr>
                        <th>Descrição</th>
                        <th style="text-align:right;">
                            Valor
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($payment->invoice->items as $item)

                    <tr>
                        <td>
                            {{ $item->description }}
                        </td>

                        <td style="text-align:right;">
                            {{ number_format((float) $item->total, 2, ',', '.') }}
                            {{ $payment->currency ?? 'AOA' }}
                        </td>
                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        @endif

        <div class="amount-box">

            <span class="amount-label">
                VALOR RECEBIDO
            </span>

            <span class="amount">
                {{ number_format((float) $payment->amount, 2, ',', '.') }}
                {{ $payment->currency ?? 'AOA' }}
            </span>

        </div>

        <div class="footer">

            <div>
                <strong>Operador</strong><br>

                {{ $payment->user?->name ?? '—' }}
            </div>

            <div style="text-align:right;">
                <strong>Data de emissão</strong><br>

                {{ now()->format('d/m/Y H:i') }}
            </div>

        </div>

        <div class="notice">
            Este documento comprova o recebimento do valor indicado,
            sujeito às condições e registos financeiros da universidade.
        </div>

    </div>

</body>
</html>
