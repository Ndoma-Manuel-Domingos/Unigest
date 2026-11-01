<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Gestão de Departamentos
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

            <div class="bg-white p-6 shadow-sm rounded-lg">

                <div class="flex justify-between items-center mb-5">
                    <h3 class="font-bold text-lg">Departamentos cadastrados</h3>

                    @can('departments.create')
                    <a href="{{ route('university.departments.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Novo Departamento
                    </a>
                    @endcan
                </div>

                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3 mb-5">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar departamento..." class="border-gray-300 rounded-md">

                    <select name="faculty_id" class="border-gray-300 rounded-md">
                        <option value="">Todas as faculdades</option>
                        @foreach($faculties as $faculty)
                        <option value="{{ $faculty->id }}" @selected((string) request('faculty_id')===(string) $faculty->id)>
                            {{ $faculty->name }}
                        </option>
                        @endforeach
                    </select>

                    <div class="flex gap-2">
                        <select name="status" class="border-gray-300 rounded-md w-full">
                            <option value="">Todos os estados</option>
                            <option value="1" @selected(request('status')==='1' )>Activos</option>
                            <option value="0" @selected(request('status')==='0' )>Inactivos</option>
                        </select>

                        <button class="px-4 py-2 bg-gray-800 text-white rounded">
                            Filtrar
                        </button>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="p-3">Departamento</th>
                                <th class="p-3">Sigla</th>
                                <th class="p-3">Faculdade</th>
                                <th class="p-3">Estado</th>
                                <th class="p-3 text-right">Acções</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($departments as $department)
                            <tr class="border-b">
                                <td class="p-3 font-medium">
                                    {{ $department->name }}
                                </td>

                                <td class="p-3">
                                    {{ $department->acronym ?: '-' }}
                                </td>

                                <td class="p-3">
                                    {{ $department->faculty->name }}
                                </td>

                                <td class="p-3">
                                    @if($department->is_active)
                                    <span class="text-green-700">Activo</span>
                                    @else
                                    <span class="text-red-700">Inactivo</span>
                                    @endif
                                </td>

                                <td class="p-3 text-right whitespace-nowrap">
                                    @can('departments.update')
                                    <a href="{{ route('university.departments.edit', $department->id) }}" class="text-indigo-600 mr-2">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('university.departments.status', $department->id) }}" class="inline">
                                        @csrf
                                        @method('PATCH')

                                        <button class="text-orange-600 mr-2">
                                            {{ $department->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                    @endcan

                                    @can('departments.delete')
                                    <form method="POST" action="{{ route('university.departments.destroy', $department->id) }}" class="inline" onsubmit="return confirm('Deseja eliminar este departamento?')">
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
                                    Nenhum departamento encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-5">
                    {{ $departments->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
