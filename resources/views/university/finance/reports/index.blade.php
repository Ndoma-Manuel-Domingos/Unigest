<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Relatórios financeiros
                </h2>
                <p class="text-sm text-gray-500">
                    Resumo financeiro da universidade.
                </p>
            </div>
        </div>
    </x-slot>

    @php
    $methodLabels = [
    'cash' => 'Dinheiro',
    'bank_transfer' => 'Transferência bancária',
    'multicaixa' => 'Multicaixa',
    'pos' => 'POS',
    'reference' => 'Referência',
    'other' => 'Outro',
    ];
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- FILTROS --}}
            <div class="rounded-lg bg-white p-6 shadow">
                <form method="GET" action="{{ route('university.financial-reports.index') }}" class="grid grid-cols-1 items-end gap-4 md:grid-cols-3">

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Data inicial
                        </label>
                        <input type="date" name="date_from" value="{{ $dateFrom->format('Y-m-d') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Data final
                        </label>
                        <input type="date" name="date_to" value="{{ $dateTo->format('Y-m-d') }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">
                            Filtrar
                        </button>

                        <a href="{{ route('university.financial-reports.index') }}" class="rounded-md bg-gray-200 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-300">
                            Limpar
                        </a>
                    </div>
                </form>

                <p class="mt-4 text-sm text-gray-500">
                    Período:
                    {{ $dateFrom->format('d/m/Y') }}
                    até
                    {{ $dateTo->format('d/m/Y') }}
                </p>
            </div>

            {{-- RESUMO FINANCEIRO --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-sm font-medium text-gray-500">
                        Receitas recebidas
                    </p>
                    <p class="mt-2 text-2xl font-bold text-green-600">
                        {{ number_format($totalRevenue, 2, ',', '.') }} AOA
                    </p>
                    <p class="mt-2 text-xs text-gray-500">
                        {{ $paymentsCount }} pagamento(s) confirmado(s)
                    </p>
                </div>

                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-sm font-medium text-gray-500">
                        Despesas pagas
                    </p>
                    <p class="mt-2 text-2xl font-bold text-red-600">
                        {{ number_format($totalExpensesPaid, 2, ',', '.') }} AOA
                    </p>
                    <p class="mt-2 text-xs text-gray-500">
                        {{ $expensePaymentsCount }} pagamento(s) confirmado(s)
                    </p>
                </div>

                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-sm font-medium text-gray-500">
                        Saldo financeiro do período
                    </p>
                    <p class="mt-2 text-2xl font-bold {{ $financialBalance >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        {{ number_format($financialBalance, 2, ',', '.') }} AOA
                    </p>
                    <p class="mt-2 text-xs text-gray-500">
                        Receitas menos despesas pagas
                    </p>
                </div>

                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-sm font-medium text-gray-500">
                        Saldo dos movimentos de caixa
                    </p>
                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ number_format($cashIncome - $cashExpenses, 2, ',', '.') }} AOA
                    </p>
                    <p class="mt-2 text-xs text-gray-500">
                        Entradas menos saídas registadas nas caixas
                    </p>
                </div>

            </div>

            {{-- RECEITAS POR MÉTODO --}}
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">
                        Receitas por método de pagamento
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                        Método
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                        Total
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @forelse ($revenueByMethod as $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $methodLabels[$item->payment_method] ?? $item->payment_method }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                        {{ number_format($item->total, 2, ',', '.') }} AOA
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-5 text-center text-sm text-gray-500">
                                        Não existem receitas neste período.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- DESPESAS POR MÉTODO --}}
                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">
                        Despesas por método de pagamento
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                        Método
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                        Total
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @forelse ($expensesByMethod as $item)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $methodLabels[$item->payment_method] ?? $item->payment_method }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                        {{ number_format($item->total, 2, ',', '.') }} AOA
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-5 text-center text-sm text-gray-500">
                                        Não existem pagamentos de despesas neste período.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- MOVIMENTOS DE CAIXA --}}
            <div class="rounded-lg bg-white p-6 shadow">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Movimentos de caixa
                    </h3>
                    <p class="text-sm text-gray-500">
                        Entradas e saídas registadas no período seleccionado.
                    </p>
                </div>

                <div class="mb-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="rounded-lg bg-green-50 p-4">
                        <p class="text-sm text-green-700">Total de entradas</p>
                        <p class="mt-1 text-xl font-bold text-green-700">
                            {{ number_format($cashIncome, 2, ',', '.') }} AOA
                        </p>
                    </div>

                    <div class="rounded-lg bg-red-50 p-4">
                        <p class="text-sm text-red-700">Total de saídas</p>
                        <p class="mt-1 text-xl font-bold text-red-700">
                            {{ number_format($cashExpenses, 2, ',', '.') }} AOA
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                    Data
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                    Descrição
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                    Utilizador
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">
                                    Tipo
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                    Valor
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse ($cashMovements as $movement)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                    {{ $movement->movement_date?->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $movement->description }}
                                </td>

                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $movement->user?->name ?? '—' }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @if ($movement->type === 'income')
                                    <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">
                                        Entrada
                                    </span>
                                    @else
                                    <span class="rounded-full bg-red-100 px-2 py-1 text-xs font-semibold text-red-700">
                                        Saída
                                    </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold {{ $movement->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($movement->amount, 2, ',', '.') }} AOA
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                                    Não existem movimentos de caixa neste período.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $cashMovements->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
