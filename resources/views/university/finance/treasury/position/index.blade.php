<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Posição Financeira da Tesouraria
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Caixa, bancos e movimentos financeiros por moeda.
                </p>
            </div>

        </div>

    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Filtro --}}
            <div class="bg-white rounded-xl shadow-sm border p-5 mb-6">

                <form method="GET" action="{{ route('university.treasury.position.index') }}" class="flex flex-col sm:flex-row sm:items-end gap-4">

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Data dos movimentos
                        </label>

                        <input type="date" name="date" value="{{ $date }}" class="rounded-lg border-gray-300" required>

                    </div>

                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                        Consultar
                    </button>

                </form>

            </div>

            {{-- Aviso de moeda --}}
            <div class="mb-6 rounded-xl bg-blue-50 border border-blue-200 p-5 text-blue-800">

                <p class="font-semibold">
                    Posição financeira por moeda
                </p>

                <p class="text-sm mt-1">
                    Os saldos de AOA, USD, EUR e outras moedas são apresentados
                    separadamente e nunca são somados entre si.
                </p>

            </div>

            {{-- Caixa --}}
            <div class="bg-white rounded-xl shadow-sm border overflow-hidden mb-6">

                <div class="px-6 py-5 border-b">

                    <div class="flex items-center justify-between">

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Caixa
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Caixas atualmente abertas.
                            </p>
                        </div>

                        <div class="text-right">

                            <p class="text-xs text-gray-500">
                                Disponível em caixa
                            </p>

                            <p class="text-xl font-bold text-gray-900">
                                {{ number_format($cashBalance, 2, ',', '.') }}
                                AOA
                            </p>

                        </div>

                    </div>

                </div>

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Caixa
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Operador
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Abertura
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Saldo Esperado
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                    Estado
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse($cashRegisters as $register)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">
                                    Caixa #{{ $register->id }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $register->user?->name ?? '—' }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $register->opened_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-right font-semibold">
                                    {{ number_format($register->expected_balance ?? 0, 2, ',', '.') }}
                                    AOA
                                </td>

                                <td class="px-6 py-4 text-center">

                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Aberto
                                    </span>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">

                                    Não existem caixas abertas.

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            {{-- Bancos por moeda --}}
            @foreach($currencies as $currency)

            @php
            $bankData = $bankByCurrency->get($currency);
            $dailyBank = $bankDaily->get($currency);

            $receiptData = $receiptsByCurrency->get($currency);
            $expenseData = $expensesByCurrency->get($currency);

            $bankBalance = $bankData['balance'] ?? 0;
            $bankIncome = $dailyBank['income'] ?? 0;
            $bankExpense = $dailyBank['expense'] ?? 0;

            $receiptTotal = $receiptData['total'] ?? 0;
            $expenseTotal = $expenseData['total'] ?? 0;
            @endphp

            @if(
            $bankData ||
            $dailyBank ||
            $receiptData ||
            $expenseData
            )

            <div class="bg-white rounded-xl shadow-sm border overflow-hidden mb-6">

                <div class="px-6 py-5 border-b">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                        <div>

                            <h3 class="text-lg font-semibold text-gray-800">
                                Bancos — {{ $currency }}
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Posição bancária e movimentos.
                            </p>

                        </div>

                        <div class="text-right">

                            <p class="text-xs text-gray-500">
                                Saldo bancário atual
                            </p>

                            <p class="text-2xl font-bold text-gray-900">
                                {{ number_format($bankBalance, 2, ',', '.') }}
                                {{ $currency }}
                            </p>

                        </div>

                    </div>

                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-5 p-6">

                    <div class="rounded-xl bg-green-50 border border-green-200 p-5">

                        <p class="text-sm text-green-700">
                            Entradas bancárias
                        </p>

                        <p class="text-xl font-bold text-green-800 mt-2">
                            {{ number_format($bankIncome, 2, ',', '.') }}
                            {{ $currency }}
                        </p>

                    </div>

                    <div class="rounded-xl bg-red-50 border border-red-200 p-5">

                        <p class="text-sm text-red-700">
                            Saídas bancárias
                        </p>

                        <p class="text-xl font-bold text-red-800 mt-2">
                            {{ number_format($bankExpense, 2, ',', '.') }}
                            {{ $currency }}
                        </p>

                    </div>

                    <div class="rounded-xl bg-blue-50 border border-blue-200 p-5">

                        <p class="text-sm text-blue-700">
                            Recebimentos
                        </p>

                        <p class="text-xl font-bold text-blue-800 mt-2">
                            {{ number_format($receiptTotal, 2, ',', '.') }}
                            {{ $currency }}
                        </p>

                    </div>

                    <div class="rounded-xl bg-gray-50 border border-gray-200 p-5">

                        <p class="text-sm text-gray-600">
                            Pagamentos
                        </p>

                        <p class="text-xl font-bold text-gray-800 mt-2">
                            {{ number_format($expenseTotal, 2, ',', '.') }}
                            {{ $currency }}
                        </p>

                    </div>

                </div>

                {{-- Contas --}}
                @if($bankData)

                <div class="border-t">

                    <div class="px-6 py-4 bg-gray-50">

                        <h4 class="font-semibold text-gray-800">
                            Contas Bancárias
                        </h4>

                    </div>

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-gray-200">

                            <thead>

                                <tr>

                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        Conta
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        Banco
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                        Moeda
                                    </th>

                                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                        Saldo
                                    </th>

                                    <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                        Estado
                                    </th>

                                </tr>

                            </thead>

                            <tbody class="divide-y divide-gray-200">

                                @foreach($bankData['accounts'] as $account)

                                <tr class="hover:bg-gray-50">

                                    <td class="px-6 py-4 font-medium">
                                        {{ $account->name }}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ $account->bank_name }}
                                    </td>

                                    <td class="px-6 py-4">
                                        {{ strtoupper($account->currency) }}
                                    </td>

                                    <td class="px-6 py-4 text-right font-semibold">
                                        {{ number_format($account->current_balance, 2, ',', '.') }}
                                        {{ strtoupper($account->currency) }}
                                    </td>

                                    <td class="px-6 py-4 text-center">

                                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            Activa
                                        </span>

                                    </td>

                                </tr>

                                @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

                @endif

            </div>

            @endif

            @endforeach

            {{-- Aviso de interpretação --}}
            <div class="rounded-xl bg-yellow-50 border border-yellow-200 p-5 text-yellow-800 mb-6">

                <p class="font-semibold">
                    Atenção
                </p>

                <p class="text-sm mt-1">
                    Recebimentos e pagamentos representam movimentos financeiros.
                    Não devem ser somados novamente aos saldos bancários para
                    calcular o saldo total da Tesouraria.
                </p>

            </div>

        </div>

    </div>

</x-app-layout>
