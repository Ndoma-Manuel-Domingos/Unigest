<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Fechos Diários da Tesouraria
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Controlo diário dos valores da tesouraria.
                </p>
            </div>

            @can('finance.treasury.closures.create')
            <a href="{{ route('university.treasury.daily-closures.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                Novo Fecho
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3">
                {{ session('success') }}
            </div>
            @endif

            @if(session('info'))
            <div class="mb-4 rounded-lg bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3">
                {{ session('info') }}
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Data
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Saldo Inicial
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Entradas
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Saídas
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Esperado
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Contado
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Diferença
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase">
                                    Estado
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Ações
                                </th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">

                            @forelse($closures as $closure)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $closure->closure_date->format('d/m/Y') }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    {{ number_format($closure->opening_balance, 2, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 text-right text-green-600">
                                    {{ number_format($closure->total_income, 2, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 text-right text-red-600">
                                    {{ number_format($closure->total_expense, 2, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 text-right font-semibold">
                                    {{ number_format($closure->expected_balance, 2, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    {{ number_format($closure->counted_balance, 2, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 text-right font-semibold
                                        {{ $closure->difference == 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ number_format($closure->difference, 2, ',', '.') }}
                                </td>

                                <td class="px-6 py-4 text-center">

                                    @if($closure->status === 'closed')
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Fechado
                                    </span>
                                    @else
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                        Aberto
                                    </span>
                                    @endif

                                </td>

                                <td class="px-6 py-4 text-right">

                                    <a href="{{ route('university.treasury.daily-closures.show', $closure) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        Ver
                                    </a>

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                    Nenhum fecho diário registado.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

                @if($closures->hasPages())
                <div class="px-6 py-4 border-t">
                    {{ $closures->links() }}
                </div>
                @endif

            </div>

        </div>
    </div>

</x-app-layout>
