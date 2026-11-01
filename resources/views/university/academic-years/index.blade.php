<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Anos Lectivos
            </h2>

            @can('academic_years.create')
            <a href="{{ route('university.academic-years.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                + Novo Ano Lectivo
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 bg-green-100 text-green-800 p-3 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 bg-red-100 text-red-800 p-3 rounded">
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white p-5 rounded-lg shadow mb-5">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar ano lectivo..." class="rounded-lg border-gray-300">

                    <select name="status" class="rounded-lg border-gray-300">
                        <option value="">Todos os estados</option>
                        <option value="active" @selected(request('status')==='active' )>Activo</option>
                        <option value="inactive" @selected(request('status')==='inactive' )>Inactivo</option>
                    </select>

                    <select name="current" class="rounded-lg border-gray-300">
                        <option value="">Todos os anos</option>
                        <option value="yes" @selected(request('current')==='yes' )>Ano actual</option>
                        <option value="no" @selected(request('current')==='no' )>Não actual</option>
                    </select>

                    <div class="flex gap-2">
                        <button class="bg-gray-800 text-white px-4 py-2 rounded-lg">
                            Filtrar
                        </button>

                        <a href="{{ route('university.academic-years.index') }}" class="bg-gray-200 px-4 py-2 rounded-lg">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">Ano Lectivo</th>
                            <th class="px-6 py-3 text-left">Início</th>
                            <th class="px-6 py-3 text-left">Fim</th>
                            <th class="px-6 py-3 text-center">Turmas</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                            <th class="px-6 py-3 text-center">Actual</th>
                            <th class="px-6 py-3 text-right">Acções</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($years as $year)
                        <tr>
                            <td class="px-6 py-4 font-medium">
                                {{ $year->name }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $year->start_date->format('d/m/Y') }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $year->end_date->format('d/m/Y') }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                {{ $year->classes_count }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($year->is_active)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">Activo</span>
                                @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">Inactivo</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($year->is_current)
                                <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-xs">Actual</span>
                                @else
                                <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                @can('academic_years.update')
                                <a href="{{ route('university.academic-years.edit', $year) }}" class="text-indigo-600 hover:underline mr-3">
                                    Editar
                                </a>

                                @if($year->is_active && !$year->is_current)
                                <form method="POST" action="{{ route('university.academic-years.current', $year) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-green-600 hover:underline mr-3">
                                        Definir actual
                                    </button>
                                </form>
                                @endif

                                <form method="POST" action="{{ route('university.academic-years.status', $year) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-orange-600 hover:underline">
                                        {{ $year->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                                @endcan

                                @can('academic_years.delete')
                                <form method="POST" action="{{ route('university.academic-years.destroy', $year) }}" class="inline" onsubmit="return confirm('Tem a certeza que deseja eliminar este ano lectivo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline ml-3">
                                        Eliminar
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                Nenhum ano lectivo encontrado.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $years->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
