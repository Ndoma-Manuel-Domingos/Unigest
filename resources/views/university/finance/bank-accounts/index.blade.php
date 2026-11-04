<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Contas Bancárias
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Gestão das contas bancárias da universidade
                </p>
            </div>

            @can('finance.bank-accounts.create')
            <a href="{{ route('university.bank-accounts.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                Nova Conta Bancária
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

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="text-sm text-gray-500">
                        Total de contas
                    </div>

                    <div class="text-2xl font-bold text-gray-800 mt-1">
                        {{ $accounts->total() }}
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="text-sm text-gray-500">
                        Contas activas
                    </div>

                    <div class="text-2xl font-bold text-green-600 mt-1">
                        {{ $accounts->getCollection()->where('is_active', true)->count() }}
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-5">
                    <div class="text-sm text-gray-500">
                        Saldo apresentado
                    </div>

                    <div class="text-2xl font-bold text-indigo-600 mt-1">
                        {{ number_format($accounts->sum('current_balance'), 2, ',', '.') }}
                        AOA
                    </div>
                </div>

            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Conta
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Banco
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    IBAN
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                    Saldo
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">
                                    Estado
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                    Acções
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">

                            @forelse($accounts as $account)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">
                                        {{ $account->name }}
                                    </div>

                                    @if($account->is_default)
                                    <span class="inline-flex mt-1 px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">
                                        Conta principal
                                    </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $account->bank_name }}

                                    @if($account->account_number)
                                    <div class="text-xs text-gray-500">
                                        Nº {{ $account->account_number }}
                                    </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $account->iban ?: '—' }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="font-semibold text-gray-900">
                                        {{ number_format($account->current_balance, 2, ',', '.') }}
                                        {{ $account->currency }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-center">

                                    @if($account->is_active)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Activa
                                    </span>
                                    @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                        Inactiva
                                    </span>
                                    @endif

                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap">

                                    <a href="{{ route('university.bank-accounts.show', $account) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Ver
                                    </a>

                                    @can('finance.bank-accounts.update')
                                    <a href="{{ route('university.bank-accounts.edit', $account) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        Editar
                                    </a>
                                    @endcan

                                    @can('finance.bank-accounts.delete')
                                    @if($account->movements_count == 0)
                                    <form action="{{ route('university.bank-accounts.destroy', $account) }}" method="POST" class="inline" onsubmit="return confirm('Tem certeza que deseja eliminar esta conta bancária?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Eliminar
                                        </button>
                                    </form>
                                    @endif
                                    @endcan

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    Nenhuma conta bancária cadastrada.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

                @if($accounts->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $accounts->links() }}
                </div>
                @endif

            </div>

        </div>
    </div>
</x-app-layout>
