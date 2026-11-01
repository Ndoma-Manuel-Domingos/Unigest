<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ $expenseCategory->name }}
                </h2>
                <p class="text-sm text-gray-500">Detalhes da categoria de despesas.</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('university.expense-categories.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">
                    Voltar
                </a>

                @can('finance.expense-categories.update')
                <a href="{{ route('university.expense-categories.edit', $expenseCategory) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white">
                    Editar
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Informações da categoria</h3>

                    @if($expenseCategory->is_active)
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs text-green-700">
                        Activa
                    </span>
                    @else
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-600">
                        Inactiva
                    </span>
                    @endif
                </div>

                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div>
                        <p class="text-sm text-gray-500">Nome</p>
                        <p class="mt-1 font-medium text-gray-900">
                            {{ $expenseCategory->name }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Código</p>
                        <p class="mt-1 font-medium text-gray-900">
                            {{ $expenseCategory->code }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Total de despesas</p>
                        <p class="mt-1 font-medium text-gray-900">
                            {{ $expenseCategory->expenses()->count() }}
                        </p>
                    </div>

                    <div class="md:col-span-3">
                        <p class="text-sm text-gray-500">Descrição</p>
                        <p class="mt-1 text-gray-900">
                            {{ $expenseCategory->description ?: 'Sem descrição.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b px-6 py-4">
                    <h3 class="font-semibold text-gray-800">Últimas despesas da categoria</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">Número</th>
                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">Descrição</th>
                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">Data</th>
                                <th class="px-6 py-3 text-right text-xs uppercase text-gray-500">Valor</th>
                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">Estado</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($expenseCategory->expenses as $expense)
                            <tr>
                                <td class="px-6 py-4 text-sm">
                                    {{ $expense->expense_number }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ $expense->description }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ $expense->expense_date?->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    {{ number_format((float) $expense->amount, 2, ',', '.') }}
                                    {{ $expense->currency }}
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    {{ ucfirst(str_replace('_', ' ', $expense->status)) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    Ainda não existem despesas nesta categoria.
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
