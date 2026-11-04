<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Fundos de Caixa Pequena</h2>
                <p class="text-sm text-gray-500 mt-1">Gestão dos fundos e dos respectivos responsáveis.</p>
            </div>

            @can('finance.petty-cash.funds.create')
            <a href="{{ route('university.petty-cash.funds.create') }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm">
                Novo fundo
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-5 p-4 rounded-lg bg-green-50 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Fundo</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Responsável</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Departamento</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Autorizado</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Saldo actual</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Acções</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($funds as $fund)
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-4">
                                    <p class="font-medium text-gray-900">{{ $fund->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $fund->code }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $fund->responsible?->name ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $fund->department?->name ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-right text-sm">
                                    {{ number_format((float) $fund->authorized_amount, 2, ',', '.') }}
                                    {{ $fund->currency }}
                                </td>
                                <td class="px-5 py-4 text-right text-sm font-semibold text-gray-900">
                                    {{ number_format((float) $fund->current_balance, 2, ',', '.') }}
                                    {{ $fund->currency }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($fund->is_active)
                                    <span class="px-2.5 py-1 rounded-full text-xs bg-green-100 text-green-700">Activo</span>
                                    @else
                                    <span class="px-2.5 py-1 rounded-full text-xs bg-gray-100 text-gray-700">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('university.petty-cash.funds.show', $fund) }}" class="text-indigo-600 text-sm font-medium hover:text-indigo-800">
                                        Consultar
                                    </a>

                                    @can('finance.petty-cash.funds.update')
                                    <a href="{{ route('university.petty-cash.funds.edit', $fund) }}" class="ml-3 text-gray-600 text-sm hover:text-gray-900">
                                        Editar
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-10 text-center text-gray-500">
                                    Ainda não existem fundos de caixa pequena.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-5 border-t">{{ $funds->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
