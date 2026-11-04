<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Transacções Bancárias
                </h2>
                <p class="text-sm text-gray-500">
                    Transacções recebidas através das integrações bancárias.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="p-4 rounded-lg bg-green-100 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if(session('warning'))
            <div class="p-4 rounded-lg bg-yellow-100 text-yellow-800">
                {{ session('warning') }}
            </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                ['Total', $summary->total, 'gray'],
                ['Confirmadas', $summary->confirmed, 'green'],
                ['Por associar', $summary->unmatched, 'yellow'],
                ['Com erro', $summary->failed, 'red'],
                ] as [$label, $value, $color])
                <div class="bg-white shadow-sm rounded-xl p-5 border-l-4 border-{{ $color }}-500">
                    <p class="text-sm text-gray-500">{{ $label }}</p>
                    <p class="text-2xl font-bold text-gray-800">
                        {{ number_format($value, 0, ',', '.') }}
                    </p>
                </div>
                @endforeach
            </div>

            <div class="bg-white shadow-sm rounded-xl p-5">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3">

                    <select name="bank_account_id" class="rounded-lg border-gray-300">
                        <option value="">Todas as contas</option>
                        @foreach($accounts as $account)
                        <option value="{{ $account->id }}" @selected(request('bank_account_id')==$account->id)>
                            {{ $account->name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="status" class="rounded-lg border-gray-300">
                        <option value="">Todos os estados</option>
                        @foreach(['confirmed' => 'Confirmada', 'unmatched' => 'Por associar', 'failed' => 'Com erro'] as $key => $label)
                        <option value="{{ $key }}" @selected(request('status')===$key)>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>

                    <select name="type" class="rounded-lg border-gray-300">
                        <option value="">Todos os tipos</option>
                        <option value="income" @selected(request('type')==='income' )>Entrada</option>
                        <option value="expense" @selected(request('type')==='expense' )>Saída</option>
                    </select>

                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border-gray-300">

                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border-gray-300">

                    <div class="flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Referência ou descrição" class="min-w-0 w-full rounded-lg border-gray-300">

                        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
                            Filtrar
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Data</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Referência</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Banco / Conta</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500">Descrição</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Valor</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500">Estado</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500">Acção</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($transactions as $item)
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    {{ \Carbon\Carbon::parse($item->occurred_at)->format('d/m/Y H:i') }}
                                </td>

                                <td class="px-4 py-3 text-sm font-medium">
                                    {{ $item->reference }}
                                    <div class="text-xs text-gray-400">
                                        {{ $item->external_transaction_id }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    {{ $item->provider }}
                                    <div class="text-xs text-gray-500">
                                        {{ $item->bank_account_name }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-sm">
                                    {{ $item->description }}
                                </td>

                                <td class="px-4 py-3 text-sm text-right font-semibold">
                                    {{ number_format($item->amount, 2, ',', '.') }}
                                    {{ $item->currency }}
                                </td>

                                <td class="px-4 py-3 text-center">
                                    @php
                                    $states = [
                                    'confirmed' => ['Confirmada', 'bg-green-100 text-green-800'],
                                    'unmatched' => ['Por associar', 'bg-yellow-100 text-yellow-800'],
                                    'failed' => ['Com erro', 'bg-red-100 text-red-800'],
                                    'matched' => ['Associada', 'bg-blue-100 text-blue-800'],
                                    ];
                                    [$stateLabel, $stateClass] = $states[$item->status] ?? [$item->status, 'bg-gray-100 text-gray-700'];
                                    @endphp

                                    <span class="px-2 py-1 rounded-full text-xs {{ $stateClass }}">
                                        {{ $stateLabel }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('university.bank-api-transactions.show', $item->id) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        Consultar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                    Nenhuma transacção bancária encontrada.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
