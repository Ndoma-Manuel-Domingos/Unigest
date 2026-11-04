<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Tesouraria
                </h2>
                <p class="text-sm text-gray-500">
                    Gestão central de recebimentos, pagamentos, caixas e bancos.
                </p>
            </div>

            <span class="text-sm text-gray-500">
                {{ now()->format('d/m/Y') }}
            </span>
        </div>
    </x-slot>

    @php
    $money = fn ($value) => number_format((float) $value, 2, ',', '.') . ' AOA';
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- INDICADORES PRINCIPAIS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <p class="text-sm text-gray-500">Recebimentos de hoje</p>
                    <h3 class="text-2xl font-bold text-green-700 mt-2">
                        {{ $money($receiptsToday) }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-2">
                        Pagamentos confirmados
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <p class="text-sm text-gray-500">Pagamentos de hoje</p>
                    <h3 class="text-2xl font-bold text-red-600 mt-2">
                        {{ $money($expensesToday) }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-2">
                        Despesas pagas e confirmadas
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <p class="text-sm text-gray-500">Dinheiro nas caixas abertas</p>
                    <h3 class="text-2xl font-bold text-blue-700 mt-2">
                        {{ $money($cashAvailable) }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-2">
                        {{ $openCashRegisters->count() }} caixa(s) aberta(s)
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <p class="text-sm text-gray-500">Saldo bancário</p>
                    <h3 class="text-2xl font-bold text-indigo-700 mt-2">
                        {{ $money($bankBalance) }}
                    </h3>
                    <p class="text-xs text-gray-400 mt-2">
                        Contas bancárias activas
                    </p>
                </div>

            </div>

            {{-- RESUMO FINANCEIRO --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                <div class="bg-white rounded-xl shadow-sm border p-6 lg:col-span-2">
                    <h3 class="font-semibold text-gray-800 mb-5">
                        Resumo financeiro do mês
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div>
                            <p class="text-sm text-gray-500">Recebimentos</p>
                            <p class="text-xl font-bold text-green-700 mt-1">
                                {{ $money($monthlyReceipts) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Pagamentos</p>
                            <p class="text-xl font-bold text-red-600 mt-1">
                                {{ $money($monthlyExpenses) }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Saldo do período</p>
                            <p class="text-xl font-bold text-blue-700 mt-1">
                                {{ $money($monthlyReceipts - $monthlyExpenses) }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="font-semibold text-gray-800 mb-4">
                        Valores pendentes
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">
                                Facturas por liquidar
                            </p>
                            <p class="text-xl font-bold text-orange-600">
                                {{ $money($outstandingInvoices) }}
                            </p>
                            <p class="text-xs text-gray-400">
                                {{ $pendingInvoicesCount }} factura(s)
                            </p>
                        </div>

                        <div class="border-t pt-4">
                            <p class="text-sm text-gray-500">
                                Despesas por pagar
                            </p>
                            <p class="text-xl font-bold text-red-600">
                                {{ $money($pendingExpenses) }}
                            </p>
                        </div>
                    </div>
                </div>

            </div>

            {{-- CAIXAS ABERTAS --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="p-5 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800">
                            Caixas abertas
                        </h3>
                        <p class="text-sm text-gray-500">
                            Posição actual dos operadores de caixa.
                        </p>
                    </div>

                    <a href="{{ route('university.cash.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        Gerir caixas →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Operador
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Abertura
                                </th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Saldo esperado
                                </th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Estado
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($openCashRegisters as $cash)
                            <tr>
                                <td class="px-5 py-3 text-sm text-gray-800">
                                    {{ $cash->user?->name ?? 'Operador não identificado' }}
                                </td>

                                <td class="px-5 py-3 text-sm text-gray-600">
                                    {{ $cash->opened_at?->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-5 py-3 text-sm text-right font-semibold text-gray-800">
                                    {{ $money($cash->expected_balance) }}
                                </td>

                                <td class="px-5 py-3 text-center">
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                        Aberta
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-5 py-8 text-center text-sm text-gray-500">
                                    Não existem caixas abertas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- CONTAS BANCÁRIAS --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="p-5 border-b flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-gray-800">
                            Contas bancárias
                        </h3>
                        <p class="text-sm text-gray-500">
                            Saldos das contas activas.
                        </p>
                    </div>

                    <a href="{{ route('university.bank-accounts.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                        Ver contas →
                    </a>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse($bankAccounts as $account)
                    <div class="px-5 py-4 flex flex-col sm:flex-row sm:justify-between gap-2">
                        <div>
                            <p class="font-medium text-gray-800">
                                {{ $account->name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $account->bank_name }}
                                @if($account->account_number)
                                · {{ $account->account_number }}
                                @endif
                            </p>
                        </div>

                        <div class="font-semibold text-gray-800">
                            {{ number_format((float) $account->current_balance, 2, ',', '.') }}
                            {{ $account->currency }}
                        </div>
                    </div>
                    @empty
                    <p class="p-6 text-center text-sm text-gray-500">
                        Nenhuma conta bancária activa.
                    </p>
                    @endforelse
                </div>
            </div>

            {{-- MOVIMENTOS RECENTES --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-gray-800">
                        Movimentos recentes de caixa
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Data
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Descrição
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Operador
                                </th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Tipo
                                </th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Valor
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentMovements as $movement)
                            <tr>
                                <td class="px-5 py-3 text-sm text-gray-600">
                                    {{ $movement->movement_date?->format('d/m/Y H:i') ?? $movement->movement_date }}
                                </td>

                                <td class="px-5 py-3 text-sm text-gray-800">
                                    {{ $movement->description }}
                                </td>

                                <td class="px-5 py-3 text-sm text-gray-600">
                                    {{ $movement->user?->name ?? '—' }}
                                </td>

                                <td class="px-5 py-3 text-sm">
                                    @if($movement->type === 'income')
                                    <span class="text-green-700">Entrada</span>
                                    @else
                                    <span class="text-red-600">Saída</span>
                                    @endif
                                </td>

                                <td class="px-5 py-3 text-sm text-right font-semibold">
                                    {{ $money($movement->amount) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-500">
                                    Ainda não existem movimentos registados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
