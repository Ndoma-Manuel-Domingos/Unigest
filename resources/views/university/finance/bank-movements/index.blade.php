<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="font-semibold text-xl text-gray-800">
                    Movimentos Bancários
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Entradas, saídas e ajustes das contas bancárias
                </p>
            </div>

            @can('finance.bank-movements.create')
            <a href="{{ route('university.bank-movements.transfer.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-semibold hover:bg-green-700">
                Transferir entre Contas
            </a>
            @endcan

            @can('finance.bank-movements.create')
            <a href="{{ route('university.bank-movements.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
                Novo Movimento
            </a>
            @endcan

        </div>

    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-5 bg-green-100 border border-green-300 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-5 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-5 mb-6">

                <form method="GET" action="{{ route('university.bank-movements.index') }}">

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                        <div>
                            <label class="text-xs text-gray-500">
                                Conta
                            </label>

                            <select name="bank_account_id" class="mt-1 w-full rounded-md border-gray-300">

                                <option value="">Todas</option>

                                @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ request('bank_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }}
                                </option>
                                @endforeach

                            </select>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500">
                                Tipo
                            </label>

                            <select name="type" class="mt-1 w-full rounded-md border-gray-300">

                                <option value="">Todos</option>

                                <option value="income" {{ request('type') === 'income' ? 'selected' : '' }}>
                                    Entradas
                                </option>

                                <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>
                                    Saídas
                                </option>

                                <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>
                                    Ajustes
                                </option>

                            </select>
                        </div>

                        <div>
                            <label class="text-xs text-gray-500">
                                De
                            </label>

                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="mt-1 w-full rounded-md border-gray-300">
                        </div>

                        <div>
                            <label class="text-xs text-gray-500">
                                Até
                            </label>

                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="mt-1 w-full rounded-md border-gray-300">
                        </div>

                        <div>
                            <label class="text-xs text-gray-500">
                                Pesquisa
                            </label>

                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Descrição, referência..." class="mt-1 w-full rounded-md border-gray-300">
                        </div>

                    </div>

                    <div class="mt-4 flex justify-end gap-2">

                        <a href="{{ route('university.bank-movements.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-sm">
                            Limpar
                        </a>

                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm">
                            Filtrar
                        </button>

                    </div>

                </form>

            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                    Data
                                </th>

                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                    Conta
                                </th>

                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                    Descrição
                                </th>

                                <th class="px-6 py-3 text-center text-xs uppercase text-gray-500">
                                    Tipo
                                </th>

                                <th class="px-6 py-3 text-right text-xs uppercase text-gray-500">
                                    Valor
                                </th>

                                <th class="px-6 py-3 text-center text-xs uppercase text-gray-500">
                                    Reconciliação
                                </th>

                                <th class="px-6 py-3 text-right text-xs uppercase text-gray-500">
                                    Acções
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse($movements as $movement)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4 text-sm">
                                    {{ $movement->movement_date->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    <div class="font-medium">
                                        {{ $movement->bankAccount->name }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $movement->bankAccount->bank_name }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    <div>
                                        {{ $movement->description }}
                                    </div>

                                    @if($movement->reference)
                                    <div class="text-xs text-gray-500">
                                        Ref.: {{ $movement->reference }}
                                    </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-center">

                                    @if($movement->type === 'income' || $movement->type === 'transfer_in')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        {{ $movement->type === 'transfer_in' ? 'Transferência recebida' : 'Entrada' }}
                                    </span>
                                    @elseif($movement->type === 'expense' || $movement->type === 'transfer_out')
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                        {{ $movement->type === 'transfer_out' ? 'Transferência enviada' : 'Saída' }}
                                    </span>
                                    @elseif($movement->adjustment_direction === 'positive')
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                        Ajuste positivo
                                    </span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                        Ajuste negativo
                                    </span>
                                    @endif

                                </td>

                                <td class="px-6 py-4 text-right font-semibold">

                                    @if($movement->isIncome())
                                    <span class="text-green-600">
                                        + {{ number_format($movement->amount, 2, ',', '.') }}
                                    </span>
                                    @else
                                    <span class="text-red-600">
                                        - {{ number_format($movement->amount, 2, ',', '.') }}
                                    </span>
                                    @endif

                                    {{ $movement->bankAccount->currency }}

                                </td>

                                <td class="px-6 py-4 text-center">

                                    @if($movement->reconciled)

                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        Reconciliado
                                    </span>

                                    @else

                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                        Pendente
                                    </span>

                                    @endif

                                </td>

                                <td class="px-6 py-4 text-right">

                                    <a href="{{ route('university.bank-movements.show', $movement) }}" class="text-indigo-600 hover:text-indigo-900">
                                        Ver
                                    </a>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">

                                    Nenhum movimento bancário encontrado.

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if($movements->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $movements->links() }}
                </div>
                @endif

            </div>

        </div>

    </div>

</x-app-layout>
