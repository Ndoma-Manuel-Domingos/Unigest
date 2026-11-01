<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Categorias de Despesas
                </h2>
                <p class="text-sm text-gray-500">
                    Organize e classifique as despesas da universidade.
                </p>
            </div>

            @can('finance.expense-categories.create')
            <a href="{{ route('university.expense-categories.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                + Nova categoria
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
                <form method="GET" action="{{ route('university.expense-categories.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome ou código..." class="rounded-md border-gray-300">

                    <select name="status" class="rounded-md border-gray-300">
                        <option value="">Todos os estados</option>
                        <option value="active" @selected(request('status')==='active' )>
                            Activas
                        </option>
                        <option value="inactive" @selected(request('status')==='inactive' )>
                            Inactivas
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white">
                            Pesquisar
                        </button>

                        <a href="{{ route('university.expense-categories.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">
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
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Categoria
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Código
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-semibold uppercase text-gray-500">
                                    Despesas
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                    Acções
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($categories as $category)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">
                                        {{ $category->name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $category->description ?: 'Sem descrição' }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $category->code }}
                                </td>

                                <td class="px-6 py-4 text-center text-sm text-gray-700">
                                    {{ $category->expenses_count }}
                                </td>

                                <td class="px-6 py-4">
                                    @if($category->is_active)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">
                                        Activa
                                    </span>
                                    @else
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                        Inactiva
                                    </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                                    @can('finance.expense-categories.view')
                                    <a href="{{ route('university.expense-categories.show', $category) }}" class="text-indigo-600 hover:underline">
                                        Ver
                                    </a>
                                    @endcan

                                    @can('finance.expense-categories.update')
                                    <a href="{{ route('university.expense-categories.edit', $category) }}" class="ml-3 text-blue-600 hover:underline">
                                        Editar
                                    </a>
                                    @endcan

                                    @can('finance.expense-categories.delete')
                                    <form method="POST" action="{{ route('university.expense-categories.toggle-status', $category) }}" class="ml-3 inline" onsubmit="return confirm('Deseja alterar o estado desta categoria?')">
                                        @csrf
                                        @method('PATCH')

                                        <button type="submit" class="{{ $category->is_active ? 'text-red-600' : 'text-green-600' }} hover:underline">
                                            {{ $category->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                    Nenhuma categoria encontrada.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
