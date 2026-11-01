<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Factura {{ $invoice->invoice_number }}
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Detalhes da factura e respectivo histórico financeiro.
                </p>
            </div>

            <a href="{{ route('university.invoices.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                <i class="fas fa-arrow-left"></i>
                Voltar às facturas
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            @php
            $statuses = [
            'draft' => ['Rascunho', 'bg-gray-100 text-gray-700'],
            'issued' => ['Emitida', 'bg-blue-50 text-blue-700'],
            'partially_paid' => ['Parcialmente paga', 'bg-amber-50 text-amber-700'],
            'paid' => ['Paga', 'bg-green-50 text-green-700'],
            'overdue' => ['Vencida', 'bg-red-50 text-red-700'],
            'cancelled' => ['Anulada', 'bg-gray-200 text-gray-600'],
            ];

            $status = $statuses[$invoice->status] ?? [$invoice->status, 'bg-gray-100 text-gray-700'];
            @endphp

            <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <h3 class="text-2xl font-bold text-gray-800">
                                {{ $invoice->invoice_number }}
                            </h3>

                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $status[1] }}">
                                {{ $status[0] }}
                            </span>
                        </div>

                        <p class="mt-2 text-sm text-gray-500">
                            Emitida em {{ $invoice->issue_date?->format('d/m/Y') ?? '—' }}
                            <span class="mx-2">•</span>
                            Vencimento {{ $invoice->due_date?->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @can('finance.payments.create')
                        @if(in_array($invoice->status, ['issued', 'partially_paid', 'overdue']) && (float) $invoice->balance > 0)
                        <a href="{{ route('university.payments.create', $invoice) }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-700">
                            Registar pagamento
                        </a>
                        @endif
                        @endcan

                        @can('finance.payments.view')
                        <a href="{{ route('university.payments.index', ['search' => $invoice->invoice_number]) }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            Pagamentos
                        </a>
                        @endcan

                        @if($invoice->status === 'draft')
                        @can('finance.invoices.issue')
                        <form method="POST" action="{{ route('university.invoices.issue', $invoice) }}" onsubmit="return confirm('Tens a certeza de que pretendes emitir esta factura?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                                Emitir factura
                            </button>
                        </form>
                        @endcan
                        @endif

                        @if(in_array($invoice->status, ['draft', 'issued']) && (float) $invoice->amount_paid == 0)
                        @can('finance.invoices.cancel')
                        <form method="POST" action="{{ route('university.invoices.cancel', $invoice) }}" onsubmit="return confirm('Tens a certeza de que pretendes anular esta factura?')">
                            @csrf
                            @method('PATCH')

                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50">
                                <i class="fas fa-ban"></i>
                                Anular
                            </button>
                        </form>
                        @endcan
                        @endif

                        <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-print"></i>
                            Imprimir
                        </button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Total da factura</p>
                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ number_format($invoice->total, 2, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">{{ $invoice->currency }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Valor pago</p>
                    <p class="mt-2 text-2xl font-bold text-green-600">
                        {{ number_format($invoice->amount_paid, 2, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">{{ $invoice->currency }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <p class="text-sm font-medium text-gray-500">Saldo em dívida</p>
                    <p class="mt-2 text-2xl font-bold {{ (float) $invoice->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format($invoice->balance, 2, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-gray-500">{{ $invoice->currency }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:col-span-2">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="font-semibold text-gray-800">Itens da factura</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Descrição</th>
                                    <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-500">Quantidade</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Preço unitário</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Total</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @forelse($invoice->items as $item)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800">
                                        {{ $item->description }}
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-600">
                                        {{ $item->quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-600">
                                        {{ number_format($item->unit_price, 2, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-semibold text-gray-800">
                                        {{ number_format($item->total, 2, ',', '.') }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                        Esta factura ainda não tem itens.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="space-y-3 border-t border-gray-100 bg-gray-50 px-6 py-5">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Subtotal</span>
                            <span class="font-medium text-gray-800">
                                {{ number_format($invoice->subtotal, 2, ',', '.') }} {{ $invoice->currency }}
                            </span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Desconto</span>
                            <span class="font-medium text-gray-800">
                                {{ number_format($invoice->discount, 2, ',', '.') }} {{ $invoice->currency }}
                            </span>
                        </div>

                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">Multa</span>
                            <span class="font-medium text-gray-800">
                                {{ number_format($invoice->penalty, 2, ',', '.') }} {{ $invoice->currency }}
                            </span>
                        </div>

                        <div class="flex justify-between border-t border-gray-200 pt-3">
                            <span class="font-bold text-gray-800">Total</span>
                            <span class="text-lg font-bold text-indigo-700">
                                {{ number_format($invoice->total, 2, ',', '.') }} {{ $invoice->currency }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="mb-4 font-semibold text-gray-800">Dados do estudante</h3>

                        <div class="space-y-4">
                            <div>
                                <p class="text-xs font-medium uppercase text-gray-400">Nome</p>
                                <p class="mt-1 font-semibold text-gray-800">
                                    {{ $invoice->student->name ?? $invoice->student->full_name ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase text-gray-400">Número de estudante</p>
                                <p class="mt-1 text-sm text-gray-700">
                                    {{ $invoice->student->student_number ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase text-gray-400">Matrícula</p>
                                <p class="mt-1 text-sm text-gray-700">
                                    {{ $invoice->enrollment->enrollment_number ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase text-gray-400">Curso</p>
                                <p class="mt-1 text-sm text-gray-700">
                                    {{ $invoice->enrollment->course->name ?? '—' }}
                                </p>
                            </div>

                            <div>
                                <p class="text-xs font-medium uppercase text-gray-400">Ano académico</p>
                                <p class="mt-1 text-sm text-gray-700">
                                    {{ $invoice->enrollment->academicYear->name ?? '—' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="mb-3 font-semibold text-gray-800">Observações</h3>
                        <p class="whitespace-pre-line text-sm text-gray-600">
                            {{ $invoice->notes ?: 'Sem observações registadas.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {

            nav,
            header,
            button,
            form,
            a {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .py-8 {
                padding-top: 0 !important;
            }
        }

    </style>
</x-app-layout>
