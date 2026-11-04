<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800">Pedidos de reposição</h2>
            <p class="text-sm text-gray-500 mt-1">
                Consulta, aprovação e pagamento de reposições dos fundos.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="p-4 rounded-lg bg-green-50 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="p-4 rounded-lg bg-red-50 text-red-700">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-5">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fundo</label>
                        <select name="fund_id" class="w-full rounded-lg border-gray-300">
                            <option value="">Todos os fundos</option>
                            @foreach($funds as $fund)
                            <option value="{{ $fund->id }}" @selected(request('fund_id')==$fund->id)>
                                {{ $fund->name }} ({{ $fund->code }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="status" class="w-full rounded-lg border-gray-300">
                            <option value="">Todos</option>
                            @foreach(['pending' => 'Pendente', 'approved' => 'Aprovado', 'rejected' => 'Rejeitado', 'paid' => 'Pago', 'cancelled' => 'Cancelado'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status')===$value)>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white">
                            Filtrar
                        </button>

                        <a href="{{ route('university.petty-cash.replenishments.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Referência</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fundo</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Solicitante</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Solicitado</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Aprovado</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Acção</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($replenishments as $item)
                            @php
                            $statusLabels = [
                            'pending' => 'Pendente',
                            'approved' => 'Aprovado',
                            'rejected' => 'Rejeitado',
                            'paid' => 'Pago',
                            'cancelled' => 'Cancelado',
                            ];
                            @endphp

                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4 text-sm font-medium">
                                    {{ $item->request_number }}
                                    <p class="text-xs text-gray-500">{{ $item->request_date?->format('d/m/Y') }}</p>
                                </td>

                                <td class="px-5 py-4 text-sm">
                                    {{ $item->fund?->name ?? 'Fundo indisponível' }}
                                </td>

                                <td class="px-5 py-4 text-sm">
                                    {{ $item->requester?->name ?? '—' }}
                                </td>

                                <td class="px-5 py-4 text-sm text-right">
                                    {{ number_format((float) $item->requested_amount, 2, ',', '.') }}
                                    {{ $item->fund?->currency }}
                                </td>

                                <td class="px-5 py-4 text-sm text-right">
                                    @if($item->approved_amount !== null)
                                    {{ number_format((float) $item->approved_amount, 2, ',', '.') }}
                                    {{ $item->fund?->currency }}
                                    @else
                                    —
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-center text-xs">
                                    {{ $statusLabels[$item->status] ?? $item->status }}
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('university.petty-cash.replenishments.show', $item) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        Consultar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-gray-500">
                                    Não existem pedidos de reposição.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-5 border-t">
                    {{ $replenishments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
