<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Relatórios Bancários
                </h2>
                <p class="text-sm text-gray-500">
                    Resumo das operações bancárias da universidade.
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('university.bank-reports.export.excel', request()->query()) }}" class="px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-800">
                    Exportar Excel
                </a>

                <a href="{{ route('university.bank-reports.export.pdf', request()->query()) }}" class="px-4 py-2 bg-red-700 text-white rounded-lg hover:bg-red-800">
                    Exportar PDF
                </a>

                <button onclick="window.print()" class="px-4 py-2 bg-gray-800 text-white rounded-lg">
                    Imprimir
                </button>
            </div>

            <button onclick="window.print()" class="px-4 py-2 bg-gray-800 text-white rounded-lg">
                Imprimir relatório
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filtros --}}
            <div class="bg-white rounded-xl shadow-sm p-5">
                <form method="GET" action="{{ route('university.bank-reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Conta bancária
                        </label>

                        <select name="bank_account_id" class="w-full rounded-lg border-gray-300">
                            <option value="">Todas as contas</option>

                            @foreach($accounts as $account)
                            <option value="{{ $account->id }}" @selected($accountId==$account->id)>
                                {{ $account->name }} ({{ $account->currency }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Data inicial
                        </label>

                        <input type="date" name="date_from" value="{{ $dateFrom }}" required class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Data final
                        </label>

                        <input type="date" name="date_to" value="{{ $dateTo }}" required class="w-full rounded-lg border-gray-300">
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Filtrar
                        </button>

                        <a href="{{ route('university.bank-reports.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            {{-- Movimentos por moeda --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-gray-800">
                        Resumo dos movimentos bancários
                    </h3>
                    <p class="text-sm text-gray-500">
                        Período: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
                        a {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">Moeda</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500">Entradas</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500">Saídas</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500">Transferências</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500">Ajustamentos</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500">Movimentos</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($movementSummary as $row)
                            <tr>
                                <td class="px-5 py-3 font-semibold">{{ $row->currency }}</td>

                                <td class="px-5 py-3 text-right text-green-700">
                                    {{ number_format($row->income, 2, ',', '.') }}
                                </td>

                                <td class="px-5 py-3 text-right text-red-700">
                                    {{ number_format($row->expenses, 2, ',', '.') }}
                                </td>

                                <td class="px-5 py-3 text-right">
                                    {{ number_format($row->transfers, 2, ',', '.') }}
                                </td>

                                <td class="px-5 py-3 text-right">
                                    {{ number_format($row->adjustments, 2, ',', '.') }}
                                </td>

                                <td class="px-5 py-3 text-right">
                                    {{ number_format($row->total_movements, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                    Não existem movimentos bancários neste período.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Transacções API --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-gray-800">
                        Transacções recebidas pelas integrações
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">Moeda</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500">Total</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500">Confirmadas</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500">Por associar</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500">Com erro</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500">Entradas confirmadas</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($transactionSummary as $row)
                            <tr>
                                <td class="px-5 py-3 font-semibold">{{ $row->currency }}</td>
                                <td class="px-5 py-3 text-right">{{ $row->total }}</td>
                                <td class="px-5 py-3 text-right text-green-700">{{ $row->confirmed }}</td>
                                <td class="px-5 py-3 text-right text-yellow-700">{{ $row->unmatched }}</td>
                                <td class="px-5 py-3 text-right text-red-700">{{ $row->failed }}</td>
                                <td class="px-5 py-3 text-right font-semibold">
                                    {{ number_format($row->confirmed_income, 2, ',', '.') }}
                                    {{ $row->currency }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                    Não existem transacções de API neste período.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Reconciliações --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Reconciliações</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ $reconciliationSummary->total ?? 0 }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Concluídas</p>
                    <p class="text-2xl font-bold text-green-700">
                        {{ $reconciliationSummary->reconciled ?? 0 }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Em rascunho</p>
                    <p class="text-2xl font-bold text-yellow-700">
                        {{ $reconciliationSummary->draft ?? 0 }}
                    </p>
                </div>
            </div>

            {{-- Últimos movimentos --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-gray-800">
                        Últimos movimentos bancários
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">Data</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">Conta</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">Referência</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500">Tipo</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500">Valor</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500">Reconciliação</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($recentMovements as $movement)
                            <tr>
                                <td class="px-5 py-3 text-sm">
                                    {{ \Carbon\Carbon::parse($movement->movement_date)->format('d/m/Y') }}
                                </td>

                                <td class="px-5 py-3 text-sm">
                                    {{ $movement->bank_account_name }}
                                </td>

                                <td class="px-5 py-3 text-sm">
                                    {{ $movement->reference }}
                                </td>

                                <td class="px-5 py-3 text-sm">
                                    {{ [
                                            'income' => 'Entrada',
                                            'expense' => 'Saída',
                                            'transfer_in' => 'Transferência recebida',
                                            'transfer_out' => 'Transferência enviada',
                                            'adjustment' => 'Ajustamento',
                                        ][$movement->type] ?? $movement->type }}
                                </td>

                                <td class="px-5 py-3 text-sm text-right font-semibold">
                                    {{ number_format($movement->amount, 2, ',', '.') }}
                                    {{ $movement->currency }}
                                </td>

                                <td class="px-5 py-3 text-center">
                                    @if($movement->reconciled)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        Conciliado
                                    </span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                        Pendente
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                    Nenhum movimento encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <style>
        @media print {

            nav,
            aside,
            button,
            form,
            header a {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .shadow-sm {
                box-shadow: none !important;
            }
        }

    </style>
</x-app-layout>
