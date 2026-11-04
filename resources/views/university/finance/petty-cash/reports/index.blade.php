<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Relatórios da Caixa Pequena</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Análise de despesas, reposições e saldos dos fundos.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if($errors->any())
            <div class="p-4 rounded-lg bg-red-50 text-red-700">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-5">
                <form method="GET" action="{{ route('university.petty-cash.reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fundo</label>
                        <select name="fund_id" class="w-full rounded-lg border-gray-300">
                            <option value="">Todos os fundos</option>
                            @foreach($funds as $fund)
                            <option value="{{ $fund->id }}" @selected(($filters['fund_id'] ?? '' )==$fund->id)>
                                {{ $fund->name }} ({{ $fund->code }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de movimento</label>
                        <select name="type" class="w-full rounded-lg border-gray-300">
                            <option value="">Todos</option>
                            <option value="expense" @selected(($filters['type'] ?? '' )==='expense' )>Despesas</option>
                            <option value="replenishment" @selected(($filters['type'] ?? '' )==='replenishment' )>Reposições</option>
                            <option value="adjustment" @selected(($filters['type'] ?? '' )==='adjustment' )>Aberturas/Ajustes</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data inicial</label>
                        <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data final</label>
                        <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div class="md:col-span-4 flex flex-wrap justify-end gap-2">
                        <a href="{{ route('university.petty-cash.reports.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">
                            Limpar
                        </a>

                        <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm">
                            Aplicar filtros
                        </button>

                        <a href="{{ route('university.petty-cash.reports.csv', request()->query()) }}" class="px-4 py-2 rounded-lg bg-green-700 text-white text-sm">
                            Exportar CSV para Excel
                        </a>

                        <a href="{{ route('university.petty-cash.reports.print', request()->query()) }}" target="_blank" class="px-4 py-2 rounded-lg bg-gray-800 text-white text-sm">
                            Imprimir / PDF
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Resumo por moeda</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @forelse($totals as $total)
                    <div class="rounded-lg border border-gray-200 p-4">
                        <p class="text-sm font-semibold text-gray-700">{{ $total->currency }}</p>

                        <div class="mt-3 space-y-2 text-sm">
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">Despesas</span>
                                <strong class="text-red-700">
                                    {{ number_format((float) $total->expenses, 2, ',', '.') }}
                                </strong>
                            </div>

                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">Reposições</span>
                                <strong class="text-green-700">
                                    {{ number_format((float) $total->replenishments, 2, ',', '.') }}
                                </strong>
                            </div>

                            <div class="flex justify-between gap-3">
                                <span class="text-gray-500">Aberturas/Ajustes</span>
                                <strong>
                                    {{ number_format((float) $total->adjustments, 2, ',', '.') }}
                                </strong>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-sm text-gray-500">Não existem movimentos aprovados para os filtros seleccionados.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-gray-800">Posição actual dos fundos</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Saldos actuais, independentemente do período escolhido no relatório de movimentos.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fundo</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Responsável</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Departamento</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Autorizado</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo actual</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($fundBalances as $fund)
                            <tr>
                                <td class="px-5 py-4 text-sm">
                                    <p class="font-medium text-gray-900">{{ $fund->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $fund->code }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm">{{ $fund->responsible?->name ?? '—' }}</td>
                                <td class="px-5 py-4 text-sm">{{ $fund->department?->name ?? '—' }}</td>
                                <td class="px-5 py-4 text-sm text-right">
                                    {{ number_format((float) $fund->authorized_amount, 2, ',', '.') }}
                                    {{ $fund->currency }}
                                </td>
                                <td class="px-5 py-4 text-sm text-right font-semibold">
                                    {{ number_format((float) $fund->current_balance, 2, ',', '.') }}
                                    {{ $fund->currency }}
                                </td>
                                <td class="px-5 py-4 text-center text-xs">
                                    {{ $fund->is_active ? 'Activo' : 'Inactivo' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                    Não existem fundos registados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-gray-800">Movimentos aprovados</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Referência</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fundo</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Descrição</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Valor</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Utilizador</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($movements as $movement)
                            <tr>
                                <td class="px-5 py-4 text-sm font-medium">{{ $movement->movement_number }}</td>
                                <td class="px-5 py-4 text-sm">{{ $movement->movement_date?->format('d/m/Y') }}</td>
                                <td class="px-5 py-4 text-sm">{{ $movement->fund?->name }}</td>
                                <td class="px-5 py-4 text-sm">
                                    {{ $movement->description }}
                                    @if($movement->supplier_name)
                                    <p class="text-xs text-gray-500">{{ $movement->supplier_name }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    {{ [
                                            'expense' => 'Despesa',
                                            'replenishment' => 'Reposição',
                                            'adjustment' => 'Abertura/Ajuste'
                                        ][$movement->type] ?? $movement->type }}
                                </td>
                                <td class="px-5 py-4 text-sm text-right font-medium">
                                    {{ number_format((float) $movement->amount, 2, ',', '.') }}
                                    {{ $movement->fund?->currency }}
                                </td>
                                <td class="px-5 py-4 text-sm">{{ $movement->user?->name ?? '—' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-gray-500">
                                    Não existem movimentos para os filtros seleccionados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-5 border-t">
                    {{ $movements->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
