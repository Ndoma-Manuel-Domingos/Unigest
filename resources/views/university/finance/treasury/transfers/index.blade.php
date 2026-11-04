<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Transferências entre caixas</h2>
                <p class="text-sm text-gray-500 mt-1">Envios, recepções e rejeições entre operadores.</p>
            </div>

            @can('finance.treasury.transfers.create')
            <a href="{{ route('university.treasury.transfers.create') }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700">
                Nova transferência
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            <form method="GET" class="bg-white rounded-xl shadow-sm p-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-700 mb-1">Estado</label>
                    <select name="status" class="w-full rounded-lg border-gray-300">
                        <option value="">Todos</option>
                        <option value="pending" @selected(request('status')==='pending' )>Pendente</option>
                        <option value="received" @selected(request('status')==='received' )>Recebida</option>
                        <option value="rejected" @selected(request('status')==='rejected' )>Rejeitada</option>
                        <option value="cancelled" @selected(request('status')==='cancelled' )>Cancelada</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-1">Data inicial</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300">
                </div>

                <div>
                    <label class="block text-sm text-gray-700 mb-1">Data final</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300">
                </div>

                <div class="md:col-span-3 flex gap-2">
                    <button class="px-4 py-2 rounded-lg bg-gray-800 text-white text-sm">Filtrar</button>
                    <a href="{{ route('university.treasury.transfers.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">Limpar</a>
                </div>
            </form>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Referência</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Origem</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Destino</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Valor</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Acção</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($transfers as $transfer)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 text-sm font-medium text-gray-900">
                                    {{ $transfer->transfer_number }}
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $transfer->sourceCashRegister?->user?->name ?? '—' }}
                                    <div class="text-xs text-gray-400">
                                        Caixa #{{ $transfer->source_cash_register_id }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $transfer->destinationCashRegister?->user?->name ?? '—' }}
                                    <div class="text-xs text-gray-400">
                                        Caixa #{{ $transfer->destination_cash_register_id }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-right text-sm font-semibold">
                                    {{ number_format((float) $transfer->amount, 2, ',', '.') }} AOA
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ $transfer->transfer_date?->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @php
                                    $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'received' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'cancelled' => 'bg-gray-100 text-gray-700',
                                    ];

                                    $statusLabels = [
                                    'pending' => 'Pendente',
                                    'received' => 'Recebida',
                                    'rejected' => 'Rejeitada',
                                    'cancelled' => 'Cancelada',
                                    ];
                                    @endphp

                                    <span class="px-2.5 py-1 rounded-full text-xs {{ $statusClasses[$transfer->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $statusLabels[$transfer->status] ?? $transfer->status }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('university.treasury.transfers.show', $transfer) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        Consultar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-gray-500">
                                    Não existem transferências registadas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-5 border-t">{{ $transfers->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
