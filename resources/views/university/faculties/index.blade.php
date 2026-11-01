<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestão de Faculdades
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                {{ $errors->first() }}
            </div>
            @endif

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                <div class="flex justify-between items-center mb-5">
                    <h3 class="text-lg font-bold">Faculdades cadastradas</h3>

                    @can('faculties.create')
                    <a href="{{ route('university.faculties.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                        Nova Faculdade
                    </a>
                    @endcan
                </div>

                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar faculdade..." class="border-gray-300 rounded-md">

                    <select name="status" class="border-gray-300 rounded-md">
                        <option value="">Todos os estados</option>
                        <option value="1" @selected(request('status')==='1' )>Activas</option>
                        <option value="0" @selected(request('status')==='0' )>Inactivas</option>
                    </select>

                    <button class="bg-gray-800 text-white rounded-md px-4 py-2">
                        Pesquisar
                    </button>

                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3">Faculdade</th>
                                <th class="p-3">Sigla</th>
                                <th class="p-3">Departamentos</th>
                                <th class="p-3">Estado</th>
                                <th class="p-3 text-right">Acções</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($faculties as $faculty)
                            <tr class="border-b">
                                <td class="p-3 font-medium">
                                    {{ $faculty->name }}
                                </td>

                                <td class="p-3">
                                    {{ $faculty->acronym ?: '-' }}
                                </td>

                                <td class="p-3">
                                    {{ $faculty->departments_count }}
                                </td>

                                <td class="p-3">
                                    @if($faculty->is_active)
                                    <span class="text-green-700">Activa</span>
                                    @else
                                    <span class="text-red-700">Inactiva</span>
                                    @endif
                                </td>

                                <td class="p-3 text-right whitespace-nowrap">

                                    @can('faculties.update')
                                    <a href="{{ route('university.faculties.edit', $faculty->id) }}" class="text-indigo-600 mr-2">
                                        Editar
                                    </a>

                                    <form action="{{ route('university.faculties.status', $faculty->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')

                                        <button class="text-orange-600 mr-2">
                                            {{ $faculty->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                    @endcan

                                    @can('faculties.delete')
                                    <form action="{{ route('university.faculties.destroy', $faculty->id) }}" method="POST" class="inline" onsubmit="return confirm('Deseja realmente eliminar esta faculdade?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="text-red-600">
                                            Eliminar
                                        </button>
                                    </form>
                                    @endcan

                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-5 text-center text-gray-500">
                                    Nenhuma faculdade encontrada.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $faculties->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
