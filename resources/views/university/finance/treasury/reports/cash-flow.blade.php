<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <div>

                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Mapa de Recebimentos e Pagamentos
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Movimentos financeiros da Tesouraria.
                </p>

            </div>

            <a href="{{ route('university.treasury.reports.cash-flow.print', request()->query()) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
                Imprimir
            </a>

        </div>

    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Filtros --}}
            <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">

                <form method="GET" action="{{ route('university.treasury.reports.cash-flow') }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Data inicial
                            </label>

                            <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full rounded-lg border-gray-300">

                        </div>

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Data final
                            </label>

                            <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full rounded-lg border-gray-300">

                        </div>

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Tipo
                            </label>

                            <select name="type" class="w-full rounded-lg border-gray-300">

                                <option value="">
                                    Todos
                                </option>

                                <option value="income" @selected($type==='income' )>
                                    Recebimentos
                                </option>

                                <option value="expense" @selected($type==='expense' )>
                                    Pagamentos
                                </option>

                            </select>

                        </div>

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Método
                            </label>

                            <select name="method" class="w-full rounded-lg border-gray-300">

                                <option value="">
                                    Todos
                                </option>

                                <option value="cash" @selected($method==='cash' )>
                                    Caixa
                                </option>

                                <option value="bank_transfer" @selected($method==='bank_transfer' )>
                                    Transferência
                                </option>

                                <option value="card" @selected($method==='card' )>
                                    Cartão
                                </option>

                                <option value="multicaixa" @selected($method==='multicaixa' )>
                                    Multicaixa
                                </option>

                                <option value="other" @selected($method==='other' )>
                                    Outro
                                </option>

                            </select>

                        </div>

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Moeda
                            </label>

                            <select name="currency" class="w-full rounded-lg border-gray-300">

                                <option value="">
                                    Todas
                                </option>

                                <option value="AOA" @selected($currency==='AOA' )>
                                    AOA
                                </option>

                                <option value="USD" @selected($currency==='USD' )>
                                    USD
                                </option>

                                <option value="EUR" @selected($currency==='EUR' )>
                                    EUR
                                </option>

                            </select>

                        </div>

                    </div>

                    <div class="mt-4 flex flex-col sm:flex-row gap-3">

                        <div class="flex-1">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Pesquisa
                            </label>

                            <input type="text" name="search" value="{{ $search }}" placeholder="Número, referência..." class="w-full rounded-lg border-gray-300">

                        </div>

                        <div class="flex items-end">

                            <button type="submit" class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                                Filtrar
                            </button>

                        </div>

                    </div>

                </form>

            </div>

            {{-- Resumo --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-6">

                @forelse($summary as $data)

                <div class="bg-white rounded-xl shadow-sm border p-6">

                    <div class="flex items-center justify-between mb-5">

                        <h3 class="font-semibold text-gray-800">
                            {{ $data['currency'] }}
                        </h3>

                        <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">
                            {{ $data['count'] }} movimento(s)
                        </span>

                    </div>

                    <div class="space-y-3">

                        <div class="flex justify-between">

                            <span class="text-sm text-gray-500">
                                Recebimentos
                            </span>

                            <span class="font-semibold text-green-600">
                                {{ number_format($data['income'], 2, ',', '.') }}
                            </span>

                        </div>

                        <div class="flex justify-between">

                            <span class="text-sm text-gray-500">
                                Pagamentos
                            </span>

                            <span class="font-semibold text-red-600">
                                {{ number_format($data['expense'], 2, ',', '.') }}
                            </span>

                        </div>

                        <div class="border-t pt-3 flex justify-between">

                            <span class="font-semibold">
                                Resultado líquido
                            </span>

                            <span class="font-bold
                                    {{ $data['net'] >= 0
                                        ? 'text-blue-600'
                                        : 'text-red-600' }}">

                                {{ number_format($data['net'], 2, ',', '.') }}

                            </span>

                        </div>

                    </div>

                </div>

                @empty

                <div class="md:col-span-2 xl:col-span-3 bg-white rounded-xl shadow-sm border p-8 text-center text-gray-500">
                    Não existem movimentos para os filtros selecionados.
                </div>

                @endforelse

            </div>

            {{-- Tabela --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">

                <div class="px-6 py-5 border-b">

                    <h3 class="text-lg font-semibold text-gray-800">
                        Movimentos
                    </h3>

                </div>

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Data
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Tipo
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Documento
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Descrição
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Beneficiário
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Método
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Operador
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Valor
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse($results as $item)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4 whitespace-nowrap">

                                    {{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y H:i') }}

                                </td>

                                <td class="px-6 py-4">

                                    @if($item['type'] === 'income')

                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Recebimento
                                    </span>

                                    @else

                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        Pagamento
                                    </span>

                                    @endif

                                </td>

                                <td class="px-6 py-4 font-medium">
                                    {{ $item['document'] }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $item['description'] }}

                                    @if($item['reference'] !== '—')

                                    <div class="text-xs text-gray-500 mt-1">
                                        Ref.: {{ $item['reference'] }}
                                    </div>

                                    @endif

                                </td>

                                <td class="px-6 py-4">
                                    {{ $item['party'] }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ ucfirst(str_replace('_', ' ', $item['method'])) }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $item['operator'] }}
                                </td>

                                <td class="px-6 py-4 text-right font-bold
                                        {{ $item['type'] === 'income'
                                            ? 'text-green-600'
                                            : 'text-red-600' }}">

                                    {{ $item['type'] === 'income' ? '+' : '-' }}

                                    {{ number_format($item['amount'], 2, ',', '.') }}

                                    {{ $item['currency'] }}

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">

                                    Nenhum movimento encontrado.

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if($results->hasPages())

                <div class="px-6 py-4 border-t">

                    {{ $results->links() }}

                </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>
