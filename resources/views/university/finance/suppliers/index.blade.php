<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">Fornecedores</h2>
                <p class="text-sm text-gray-500">Gestão de fornecedores da universidade.</p>
            </div>

            @can('finance.suppliers.create')
            <a href="{{ route('university.suppliers.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                + Novo fornecedor
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-5 rounded-md bg-green-50 p-4 text-green-700">
                {{ session('success') }}
            </div>
            @endif

            <div class="mb-6 rounded-lg bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('university.suppliers.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar por nome, código, NIF..." class="rounded-md border-gray-300">

                    <select name="status" class="rounded-md border-gray-300">
                        <option value="">Todos os estados</option>
                        <option value="active" @selected(request('status')==='active' )>Activos</option>
                        <option value="inactive" @selected(request('status')==='inactive' )>Inactivos</option>
                    </select>

                    <div class="flex gap-2">
                        <button class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white">
                            Pesquisar
                        </button>
                        <a href="{{ route('university.suppliers.index') }}" class="rounded-md border px-4 py-2 text-sm text-gray-700">
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
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Fornecedor</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">NIF</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Contacto</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">Acções</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($suppliers as $supplier)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ $supplier->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $supplier->email }}</div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">{{ $supplier->code }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $supplier->tax_number ?: '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $supplier->phone ?: '—' }}</td>

                                <td class="px-6 py-4">
                                    @if($supplier->is_active)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-medium text-green-700">Activo</span>
                                    @else
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">Inactivo</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right text-sm whitespace-nowrap">
                                    @can('finance.suppliers.view')
                                    <a href="{{ route('university.suppliers.show', $supplier) }}" class="text-indigo-600 hover:underline">Ver</a>
                                    @endcan

                                    @can('finance.suppliers.update')
                                    <a href="{{ route('university.suppliers.edit', $supplier) }}" class="ml-3 text-blue-600 hover:underline">Editar</a>
                                    @endcan

                                    @can('finance.suppliers.delete')
                                    <form method="POST" action="{{ route('university.suppliers.toggle-status', $supplier) }}" class="ml-3 inline" onsubmit="return confirm('Deseja alterar o estado deste fornecedor?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="{{ $supplier->is_active ? 'text-red-600' : 'text-green-600' }} hover:underline">
                                            {{ $supplier->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    Nenhum fornecedor encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $suppliers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
