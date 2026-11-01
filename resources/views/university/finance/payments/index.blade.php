<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Pagamentos e Recibos</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Consultar pagamentos, confirmar recebimentos e emitir recibos.
                </p>
            </div>

            <a href="{{ route('university.invoices.index') }}" class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                <i class="fas fa-file-invoice"></i>
                Consultar facturas
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

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">

                <div class="border-b border-gray-100 px-6 py-5">
                    <h3 class="font-semibold text-gray-800">Histórico de pagamentos</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Pesquisa e acompanha todos os pagamentos registados.
                    </p>
                </div>

                {{-- Filtros --}}
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                    <form method="GET" action="{{ route('university.payments.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">

                        <div class="md:col-span-2">
                            <label for="search" class="mb-1 block text-sm font-medium text-gray-700">
                                Pesquisa
                            </label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Recibo, referência, factura ou estudante..." class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="status" class="mb-1 block text-sm font-medium text-gray-700">
                                Estado
                            </label>
                            <select name="status" id="status" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                <option value="pending" @selected(request('status')==='pending' )>Pendente</option>
                                <option value="confirmed" @selected(request('status')==='confirmed' )>Confirmado</option>
                                <option value="cancelled" @selected(request('status')==='cancelled' )>Anulado</option>
                                <option value="reversed" @selected(request('status')==='reversed' )>Revertido</option>
                            </select>
                        </div>

                        <div>
                            <label for="payment_method" class="mb-1 block text-sm font-medium text-gray-700">
                                Método
                            </label>
                            <select name="payment_method" id="payment_method" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                <option value="cash" @selected(request('payment_method')==='cash' )>Numerário</option>
                                <option value="bank_transfer" @selected(request('payment_method')==='bank_transfer' )>Transferência</option>
                                <option value="multicaixa" @selected(request('payment_method')==='multicaixa' )>Multicaixa</option>
                                <option value="pos" @selected(request('payment_method')==='pos' )>TPA / POS</option>
                                <option value="reference" @selected(request('payment_method')==='reference' )>Referência</option>
                                <option value="other" @selected(request('payment_method')==='other' )>Outro</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                <i class="fas fa-search"></i>
                                Filtrar
                            </button>

                            <a href="{{ route('university.payments.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Tabela --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Recibo
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Estudante
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Factura
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Data
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Método
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Valor
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Estado
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Acções
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($payments as $payment)
                            @php
                            $statuses = [
                            'pending' => ['Pendente', 'bg-amber-50 text-amber-700'],
                            'confirmed' => ['Confirmado', 'bg-green-50 text-green-700'],
                            'cancelled' => ['Anulado', 'bg-gray-100 text-gray-600'],
                            'reversed' => ['Revertido', 'bg-red-50 text-red-700'],
                            ];

                            $status = $statuses[$payment->status]
                            ?? [$payment->status, 'bg-gray-100 text-gray-700'];

                            $methods = [
                            'cash' => 'Numerário',
                            'bank_transfer' => 'Transferência',
                            'multicaixa' => 'Multicaixa',
                            'pos' => 'TPA / POS',
                            'reference' => 'Referência',
                            'other' => 'Outro',
                            ];
                            @endphp

                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="font-semibold text-indigo-700">
                                        {{ $payment->payment_number }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-800">
                                        {{ $payment->invoice->student->name
                                                ?? $payment->invoice->student->full_name
                                                ?? '—' }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $payment->invoice->student->student_number ?? '' }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                    {{ $payment->invoice->invoice_number ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ $payment->payment_date?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ $methods[$payment->payment_method] ?? $payment->payment_method }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <span class="font-semibold text-gray-800">
                                        {{ number_format($payment->amount, 2, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-gray-500">
                                        {{ $payment->currency }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $status[1] }}">
                                        {{ $status[0] }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">

                                        @if($payment->status === 'pending')
                                        @can('finance.payments.confirm')
                                        <form method="POST" action="{{ route('university.payments.confirm', $payment) }}" onsubmit="return confirm('Confirmar este pagamento?')">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" title="Confirmar pagamento" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-green-200 text-green-600 transition hover:bg-green-50">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        @endcan

                                        @can('finance.payments.cancel')
                                        <form method="POST" action="{{ route('university.payments.cancel', $payment) }}" onsubmit="return confirm('Anular este pagamento pendente?')">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" title="Anular pagamento" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-red-200 text-red-600 transition hover:bg-red-50">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                        @endcan
                                        @endif

                                        @if($payment->status === 'confirmed')
                                        @can('finance.payments.receipt')
                                        <a href="{{ route('university.payments.receipt', $payment) }}" title="Ver recibo" target="_blank" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-indigo-600 transition hover:bg-indigo-50">
                                            <i class="fas fa-receipt"></i>
                                        </a>
                                        @endcan

                                        @can('finance.payments.reverse')
                                        <button type="button" title="Reverter pagamento" onclick="document.getElementById('reverse-payment-{{ $payment->id }}').classList.remove('hidden')" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-amber-200 text-amber-600 transition hover:bg-amber-50">
                                            <i class="fas fa-undo"></i>
                                        </button>
                                        @endcan
                                        @endif
                                    </div>

                                    @can('finance.payments.reverse')
                                    @if($payment->status === 'confirmed')
                                    <div id="reverse-payment-{{ $payment->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/50 p-4">
                                        <div class="mx-auto mt-20 max-w-md rounded-xl bg-white p-6 shadow-xl">
                                            <div class="mb-4 flex items-center justify-between">
                                                <h3 class="font-bold text-gray-800">Reverter pagamento</h3>
                                                <button type="button" onclick="document.getElementById('reverse-payment-{{ $payment->id }}').classList.add('hidden')" class="text-gray-400 hover:text-gray-700">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>

                                            <p class="mb-4 text-sm text-gray-600">
                                                Indica o motivo da reversão do pagamento
                                                <strong>{{ $payment->payment_number }}</strong>.
                                            </p>

                                            <form method="POST" action="{{ route('university.payments.reverse', $payment) }}">
                                                @csrf
                                                @method('PATCH')

                                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                                    Motivo da reversão
                                                </label>

                                                <textarea name="observations" rows="3" required class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Descreve o motivo..."></textarea>

                                                <div class="mt-4 flex justify-end gap-2">
                                                    <button type="button" onclick="document.getElementById('reverse-payment-{{ $payment->id }}').classList.add('hidden')" class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700">
                                                        Cancelar
                                                    </button>

                                                    <button type="submit" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                                                        Confirmar reversão
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    @endif
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <i class="fas fa-money-bill-wave mb-3 text-4xl text-gray-300"></i>
                                    <p class="font-semibold text-gray-600">Nenhum pagamento encontrado</p>
                                    <p class="mt-1 text-sm text-gray-400">
                                        Os pagamentos registados aparecerão aqui.
                                    </p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($payments->hasPages())
                <div class="border-t border-gray-100 px-6 py-4">
                    {{ $payments->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
