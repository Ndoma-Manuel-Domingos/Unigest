<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Recibo de Pagamento</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Documento comprovativo do pagamento confirmado.
                </p>
            </div>

            <div class="flex gap-2">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                    <i class="fas fa-print"></i>
                    Imprimir recibo
                </button>

                <a href="{{ route('university.payments.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-arrow-left"></i>
                    Voltar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                {{-- Cabeçalho do recibo --}}
                <div class="border-b border-gray-200 px-8 py-8 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-green-50">
                        <i class="fas fa-check-circle text-3xl text-green-600"></i>
                    </div>

                    <h1 class="text-2xl font-bold uppercase tracking-wide text-gray-800">
                        Recibo de Pagamento
                    </h1>

                    <p class="mt-2 text-sm text-gray-500">
                        Comprovativo de pagamento confirmado
                    </p>

                    <div class="mt-5 inline-block rounded-lg bg-gray-50 px-5 py-3">
                        <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Número do recibo
                        </p>
                        <p class="mt-1 text-lg font-bold text-indigo-700">
                            {{ $payment->payment_number }}
                        </p>
                    </div>
                </div>

                {{-- Dados do pagamento --}}
                <div class="space-y-6 p-8">

                    <div>
                        <h3 class="mb-4 border-b border-gray-100 pb-2 text-sm font-bold uppercase tracking-wide text-gray-500">
                            Dados do estudante
                        </h3>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs text-gray-400">Nome completo</p>
                                <p class="mt-1 font-semibold text-gray-800">
                                    {{ $payment->invoice->student->name
                                        ?? $payment->invoice->student->full_name
                                        ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">Número de estudante</p>
                                <p class="mt-1 font-semibold text-gray-800">
                                    {{ $payment->invoice->student->student_number ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">Curso</p>
                                <p class="mt-1 text-sm text-gray-700">
                                    {{ $payment->invoice->enrollment->course->name ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">Ano académico</p>
                                <p class="mt-1 text-sm text-gray-700">
                                    {{ $payment->invoice->enrollment->academicYear->name ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="mb-4 border-b border-gray-100 pb-2 text-sm font-bold uppercase tracking-wide text-gray-500">
                            Dados da operação
                        </h3>

                        @php
                        $methods = [
                        'cash' => 'Numerário',
                        'bank_transfer' => 'Transferência bancária',
                        'multicaixa' => 'Multicaixa',
                        'pos' => 'TPA / POS',
                        'reference' => 'Referência',
                        'other' => 'Outro',
                        ];
                        @endphp

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs text-gray-400">Número da factura</p>
                                <p class="mt-1 font-semibold text-indigo-700">
                                    {{ $payment->invoice->invoice_number }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">Data do pagamento</p>
                                <p class="mt-1 text-sm font-medium text-gray-800">
                                    {{ $payment->payment_date?->format('d/m/Y') ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">Método de pagamento</p>
                                <p class="mt-1 text-sm font-medium text-gray-800">
                                    {{ $methods[$payment->payment_method] ?? $payment->payment_method }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">Referência da transacção</p>
                                <p class="mt-1 text-sm font-medium text-gray-800">
                                    {{ $payment->transaction_reference ?: '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400">Recebido por</p>
                                <p class="mt-1 text-sm font-medium text-gray-800">
                                    {{ $payment->receivedBy->name ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Valor --}}
                    <div class="rounded-xl border border-green-200 bg-green-50 p-6 text-center">
                        <p class="text-sm font-semibold uppercase tracking-wide text-green-700">
                            Valor recebido
                        </p>

                        <p class="mt-2 text-3xl font-extrabold text-green-800">
                            {{ number_format($payment->amount, 2, ',', '.') }}
                            <span class="text-lg">{{ $payment->currency }}</span>
                        </p>

                        <p class="mt-2 text-xs text-green-700">
                            Pagamento confirmado
                        </p>
                    </div>

                    {{-- Saldo da factura --}}
                    <div class="grid grid-cols-1 gap-4 rounded-lg border border-gray-200 p-5 sm:grid-cols-2">
                        <div>
                            <p class="text-xs text-gray-400">Total da factura</p>
                            <p class="mt-1 font-semibold text-gray-800">
                                {{ number_format($payment->invoice->total, 2, ',', '.') }}
                                {{ $payment->currency }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-400">Saldo actual da factura</p>
                            <p class="mt-1 font-semibold {{ (float) $payment->invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($payment->invoice->balance, 2, ',', '.') }}
                                {{ $payment->currency }}
                            </p>
                        </div>
                    </div>

                    @if($payment->observations)
                    <div>
                        <h3 class="mb-2 text-sm font-bold uppercase tracking-wide text-gray-500">
                            Observações
                        </h3>
                        <p class="whitespace-pre-line text-sm text-gray-700">
                            {{ $payment->observations }}
                        </p>
                    </div>
                    @endif

                    {{-- Assinaturas --}}
                    <div class="grid grid-cols-1 gap-10 pt-10 sm:grid-cols-2">
                        <div class="border-t border-gray-400 pt-3 text-center">
                            <p class="text-sm font-medium text-gray-700">
                                Assinatura do responsável
                            </p>
                        </div>

                        <div class="border-t border-gray-400 pt-3 text-center">
                            <p class="text-sm font-medium text-gray-700">
                                Assinatura do estudante
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-5 text-center">
                        <p class="text-xs text-gray-400">
                            Documento emitido pelo sistema de gestão universitária.
                        </p>
                        <p class="mt-1 text-xs text-gray-400">
                            {{ now()->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                background: white !important;
            }

            nav,
            header,
            button,
            a {
                display: none !important;
            }

            .py-8 {
                padding: 0 !important;
            }

            .max-w-3xl {
                max-width: 100% !important;
            }

            .shadow-sm {
                box-shadow: none !important;
            }

            .rounded-xl {
                border-radius: 0 !important;
            }
        }

    </style>
</x-app-layout>
