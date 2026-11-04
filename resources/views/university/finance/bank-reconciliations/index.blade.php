<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Reconciliação Bancária
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Histórico e controlo das reconciliações bancárias.
                </p>
            </div>

            @can('finance.bank-reconciliations.create')
            <a href="{{ route('university.bank-reconciliations.create') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                + Nova Reconciliação
            </a>
            @endcan

        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4 text-red-800">
                {{ session('error') }}
            </div>
            @endif

            {{-- FILTROS --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">

                <form method="GET" action="{{ route('university.bank-reconciliations.index') }}">

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Conta
                            </label>

                            <select name="bank_account_id" class="w-full rounded-lg border-gray-300">
                                <option value="">Todas</option>

                                @foreach($bankAccounts as $account)
                                <option value="{{ $account->id }}" @selected(request('bank_account_id')==$account->id)>
                                    {{ $account->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Estado
                            </label>

                            <select name="status" class="w-full rounded-lg border-gray-300">

                                <option value="">Todos</option>

                                <option value="draft" @selected(request('status')==='draft' )>
                                    Em aberto
                                </option>

                                <option value="reconciled" @selected(request('status')==='reconciled' )>
                                    Concluída
                                </option>

                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                De
                            </label>

                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Até
                            </label>

                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300">
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="px-4 py-2 rounded-lg bg-gray-800 text-white hover:bg-gray-900">
                                Filtrar
                            </button>

                            <a href="{{ route('university.bank-reconciliations.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                                Limpar
                            </a>
                        </div>

                    </div>

                </form>

            </div>

            {{-- TABELA --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Referência
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Conta
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Período
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Saldo Extrato
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Diferença
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Estado
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Ações
                                </th>
                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse($reconciliations as $reconciliation)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900">
                                        {{ $reconciliation->reference }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $reconciliation->creator?->name ?? '—' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">
                                        {{ $reconciliation->bankAccount->name }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $reconciliation->bankAccount->bank_name }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    {{ $reconciliation->period_start->format('d/m/Y') }}
                                    —
                                    {{ $reconciliation->period_end->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right font-medium">
                                    {{ number_format((float) $reconciliation->statement_closing_balance, 2, ',', '.') }}
                                    {{ $reconciliation->bankAccount->currency }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right font-semibold
                                        {{ abs((float) $reconciliation->difference) <= 0.01
                                            ? 'text-green-600'
                                            : 'text-red-600' }}">

                                    {{ number_format((float) $reconciliation->difference, 2, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">

                                    @if($reconciliation->status === 'reconciled')

                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Concluída
                                    </span>

                                    @else

                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Em aberto
                                    </span>

                                    @endif

                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-right">

                                    <a href="{{ route('university.bank-reconciliations.show', $reconciliation) }}" class="inline-flex items-center px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100">
                                        Abrir
                                    </a>

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    Nenhuma reconciliação encontrada.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if($reconciliations->hasPages())
                <div class="p-4 border-t border-gray-200">
                    {{ $reconciliations->links() }}
                </div>
                @endif

            </div>

        </div>
    </div>

</x-app-layout>
