<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Livro de Movimentos
                </h2>
                <p class="text-sm text-gray-500">
                    Consulta centralizada dos movimentos financeiros.
                </p>
            </div>

            <a href="{{ route('university.treasury.dashboard') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                ← Voltar à Tesouraria
            </a>
        </div>
    </x-slot>


    @php
    $money = fn ($value) => number_format((float) $value, 2, ',', '.');
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- RESUMO --}}
            {{-- <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="bg-white border rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Total de entradas</p>
                    <p class="text-2xl font-bold text-green-700 mt-2">
                        {{ $money($totalIncome) }}
            </p>
            <p class="text-xs text-gray-400">Soma dos registos filtrados</p>
        </div>

        <div class="bg-white border rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total de saídas</p>
            <p class="text-2xl font-bold text-red-600 mt-2">
                {{ $money($totalExpense) }}
            </p>
            <p class="text-xs text-gray-400">Soma dos registos filtrados</p>
        </div>
    </div> --}}

    {{-- RESUMO --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        {{-- ENTRADAS --}}
        <div class="bg-white border rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total de entradas</p>

            @forelse($totalIncome as $currency => $total)
            <p class="text-2xl font-bold text-green-700 mt-2">
                {{ $currency }} {{ $money($total) }}
            </p>
            @empty
            <p class="text-2xl font-bold text-green-700 mt-2">
                0,00
            </p>
            @endforelse

            <p class="text-xs text-gray-400">
                Soma dos registos filtrados
            </p>
        </div>

        {{-- SAÍDAS --}}
        <div class="bg-white border rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500">Total de saídas</p>

            @forelse($totalExpense as $currency => $total)
            <p class="text-2xl font-bold text-red-600 mt-2">
                {{ $currency }} {{ $money($total) }}
            </p>
            @empty
            <p class="text-2xl font-bold text-red-600 mt-2">
                0,00
            </p>
            @endforelse

            <p class="text-xs text-gray-400">
                Soma dos registos filtrados
            </p>
        </div>

    </div>

    {{-- FILTROS --}}
    <div class="bg-white border rounded-xl p-5 shadow-sm">
        <form method="GET" action="{{ route('university.treasury.movements.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Data inicial
                </label>
                <input type="date" name="date_from" value="{{ request('date_from', $dateFrom) }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Data final
                </label>
                <input type="date" name="date_to" value="{{ request('date_to', $dateTo) }}" class="w-full rounded-lg border-gray-300 text-sm">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tipo
                </label>
                <select name="type" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Todos</option>
                    <option value="income" @selected($selectedType==='income' )>
                        Entradas
                    </option>
                    <option value="expense" @selected($selectedType==='expense' )>
                        Saídas
                    </option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Origem
                </label>
                <select name="source" class="w-full rounded-lg border-gray-300 text-sm">
                    <option value="">Todas</option>
                    <option value="receipt" @selected($selectedSource==='receipt' )>
                        Recebimentos
                    </option>
                    <option value="expense_payment" @selected($selectedSource==='expense_payment' )>
                        Pagamentos de despesas
                    </option>
                    <option value="cash" @selected($selectedSource==='cash' )>
                        Caixa manual
                    </option>
                    <option value="bank" @selected($selectedSource==='bank' )>
                        Bancos
                    </option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg px-4 py-2 text-sm font-medium">
                    Filtrar
                </button>

                <a href="{{ route('university.treasury.movements.index') }}" class="px-4 py-2 rounded-lg border text-sm text-gray-700">
                    Limpar
                </a>
            </div>
        </form>
    </div>

    {{-- TABELA --}}
    <div class="bg-white border rounded-xl shadow-sm overflow-hidden">
        <div class="p-5 border-b flex justify-between items-center">
            <div>
                <h3 class="font-semibold text-gray-800">
                    Movimentos financeiros
                </h3>
                <p class="text-sm text-gray-500">
                    {{ $movements->total() }} registo(s) encontrado(s).
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Referência</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Descrição</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Origem</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Valor</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse($movements as $movement)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 text-sm text-gray-600 whitespace-nowrap">
                            {{ \Illuminate\Support\Carbon::parse($movement->date)->format('d/m/Y') }}
                        </td>

                        <td class="px-5 py-3 text-sm text-gray-600">
                            {{ $movement->reference }}
                        </td>

                        <td class="px-5 py-3 text-sm text-gray-800">
                            {{ $movement->description }}
                        </td>

                        <td class="px-5 py-3 text-sm text-gray-600">
                            {{ $movement->source_label }}
                        </td>

                        <td class="px-5 py-3 text-sm">
                            @if($movement->type === 'income')
                            <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs">
                                Entrada
                            </span>
                            @else
                            <span class="px-2 py-1 rounded-full bg-red-100 text-red-700 text-xs">
                                Saída
                            </span>
                            @endif
                        </td>

                        <td class="px-5 py-3 text-sm text-right font-semibold whitespace-nowrap
                                        {{ $movement->type === 'income' ? 'text-green-700' : 'text-red-600' }}">
                            {{ $money($movement->amount) }}
                            {{ $movement->currency }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">
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


    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

        <div class="bg-white border rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-3">Total de entradas</p>

            @forelse($totalIncome as $currency => $amount)
            <p class="text-xl font-bold text-green-700 mt-2">
                {{ number_format($amount, 2, ',', '.') }} {{ $currency }}
            </p>
            @empty
            <p class="text-gray-400">Sem entradas</p>
            @endforelse
        </div>

        <div class="bg-white border rounded-xl p-5 shadow-sm">
            <p class="text-sm text-gray-500 mb-3">Total de saídas</p>

            @forelse($totalExpense as $currency => $amount)
            <p class="text-xl font-bold text-red-600 mt-2">
                {{ number_format($amount, 2, ',', '.') }} {{ $currency }}
            </p>
            @empty
            <p class="text-gray-400">Sem saídas</p>
            @endforelse
        </div>

    </div>

    </div>
    </div>
</x-app-layout>
