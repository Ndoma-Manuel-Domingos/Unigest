<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Despesas</h2>
                <p class="text-sm text-gray-500">Gestão das despesas da universidade.</p>
            </div>

            @can('finance.expenses.create')
            <a href="{{ route('university.expenses.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                + Registar despesa
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="rounded-md bg-green-50 p-4 text-green-700">
                {{ session('success') }}
            </div>
            @endif

            <div class="rounded-lg bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('university.expenses.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3 lg:grid-cols-4">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Número, descrição ou referência..." class="rounded-md border-gray-300">

                    <select name="category_id" class="rounded-md border-gray-300">
                        <option value="">Todas as categorias</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id')==$category->id)>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="supplier_id" class="rounded-md border-gray-300">
                        <option value="">Todos os fornecedores</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" @selected(request('supplier_id')==$supplier->id)>
                            {{ $supplier->name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="status" class="rounded-md border-gray-300">
                        <option value="">Todos os estados</option>
                        @foreach([
                        'draft' => 'Rascunho',
                        'pending' => 'Pendente',
                        'approved' => 'Aprovada',
                        'partially_paid' => 'Parcialmente paga',
                        'paid' => 'Paga',
                        'cancelled' => 'Cancelada',
                        ] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status')===$value)>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>

                    <div>
                        <label class="mb-1 block text-xs text-gray-500">Data inicial</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="mb-1 block text-xs text-gray-500">Data final</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-md border-gray-300">
                    </div>

                    <div class="flex items-end gap-2 md:col-span-3 lg:col-span-2">
                        <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white">
                            Filtrar
                        </button>

                        <a href="{{ route('university.expenses.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Despesa</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Fornecedor / Categoria</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Data</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-500">Valor</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-500">Saldo</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-gray-500">Estado</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase text-gray-500">Acções</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($expenses as $expense)
                            <tr>
                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-900">{{ $expense->expense_number }}</div>
                                    <div class="text-sm text-gray-500">{{ $expense->description }}</div>
                                </td>

                                <td class="px-5 py-4 text-sm">
                                    <div class="text-gray-900">{{ $expense->supplier?->name ?? 'Sem fornecedor' }}</div>
                                    <div class="text-gray-500">{{ $expense->category?->name }}</div>
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $expense->expense_date?->format('d/m/Y') }}
                                </td>

                                <td class="px-5 py-4 text-right text-sm font-medium">
                                    {{ number_format((float) $expense->amount, 2, ',', '.') }}
                                    {{ $expense->currency }}
                                </td>

                                <td class="px-5 py-4 text-right text-sm">
                                    {{ number_format((float) $expense->balance, 2, ',', '.') }}
                                    {{ $expense->currency }}
                                </td>

                                <td class="px-5 py-4">
                                    @php
                                    $statusLabels = [
                                    'draft' => 'Rascunho',
                                    'pending' => 'Pendente',
                                    'approved' => 'Aprovada',
                                    'partially_paid' => 'Parcialmente paga',
                                    'paid' => 'Paga',
                                    'cancelled' => 'Cancelada',
                                    ];

                                    $statusColors = [
                                    'draft' => 'bg-gray-100 text-gray-700',
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'approved' => 'bg-blue-100 text-blue-700',
                                    'partially_paid' => 'bg-orange-100 text-orange-700',
                                    'paid' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    ];
                                    @endphp

                                    <span class="rounded-full px-3 py-1 text-xs font-medium {{ $statusColors[$expense->status] ?? 'bg-gray-100 text-gray-700' }}">
                                        {{ $statusLabels[$expense->status] ?? $expense->status }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right text-sm">
                                    @can('finance.expenses.view')
                                    <a href="{{ route('university.expenses.show', $expense) }}" class="text-indigo-600 hover:underline">Ver</a>
                                    @endcan

                                    @if($expense->status === 'draft')
                                    @can('finance.expenses.update')
                                    <a href="{{ route('university.expenses.edit', $expense) }}" class="ml-2 text-blue-600 hover:underline">Editar</a>
                                    @endcan
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                    Nenhuma despesa encontrada.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t px-6 py-4">
                    {{ $expenses->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
