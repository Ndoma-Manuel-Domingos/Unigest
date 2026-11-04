<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Controlo de Caixa por Operador
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Acompanhamento das aberturas, movimentos e fechos de caixa.
                </p>
            </div>

            <a href="{{ route('university.treasury.dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm">
                Voltar à Tesouraria
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white rounded-xl shadow-sm p-5">
                <form method="GET" action="{{ route('university.treasury.operators.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Operador</label>
                        <select name="operator_id" class="w-full rounded-lg border-gray-300">
                            <option value="">Todos os operadores</option>
                            @foreach($operators as $operator)
                            <option value="{{ $operator->id }}" @selected(request('operator_id')==$operator->id)>
                                {{ $operator->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="status" class="w-full rounded-lg border-gray-300">
                            <option value="">Todos</option>
                            <option value="open" @selected(request('status')==='open' )>Aberta</option>
                            <option value="closed" @selected(request('status')==='closed' )>Fechada</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data inicial</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data final</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div class="md:col-span-4 flex flex-wrap gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Filtrar
                        </button>

                        <a href="{{ route('university.treasury.operators.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-gray-800">Histórico de caixas</h3>
                    <p class="text-sm text-gray-500">
                        Os valores apresentados correspondem aos movimentos registados em cada caixa.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Operador</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Abertura</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo inicial</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Entradas</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saídas</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fecho</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Acção</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($registers as $register)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-900">
                                        {{ $register->user?->name ?? 'Utilizador removido' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Caixa #{{ $register->id }}
                                    </div>
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $register->opened_at?->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-5 py-4 text-right text-sm">
                                    {{ number_format((float) $register->opening_balance, 2, ',', '.') }} AOA
                                </td>

                                <td class="px-5 py-4 text-right text-sm text-green-700">
                                    {{ number_format($register->report_income, 2, ',', '.') }} AOA
                                </td>

                                <td class="px-5 py-4 text-right text-sm text-red-700">
                                    {{ number_format($register->report_expense, 2, ',', '.') }} AOA
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $register->closed_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                                <td class="px-5 py-4 text-center">
                                    @if($register->status === 'open')
                                    <span class="px-2.5 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                        Aberta
                                    </span>
                                    @else
                                    <span class="px-2.5 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                        Fechada
                                    </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-right">
                                    <a href="{{ route('university.treasury.operators.show', $register) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">
                                        Consultar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-5 py-10 text-center text-gray-500">
                                    Nenhuma caixa encontrada para os filtros seleccionados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-5 border-t">
                    {{ $registers->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
