<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Gestão de Disciplinas
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

                    <h3 class="text-lg font-bold">
                        Disciplinas
                    </h3>

                    @can('subjects.create')
                    <a href="{{ route('university.subjects.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Nova Disciplina
                    </a>
                    @endcan

                </div>

                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-5">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar..." class="border-gray-300 rounded-md">

                    <select name="department_id" class="border-gray-300 rounded-md">

                        <option value="">Departamentos</option>

                        @foreach($departments as $department)
                        <option value="{{ $department->id }}" @selected( (string) request('department_id')===(string) $department->id
                            )>
                            {{ $department->name }}
                        </option>
                        @endforeach

                    </select>

                    <select name="type" class="border-gray-300 rounded-md">

                        <option value="">Tipos</option>
                        <option value="obrigatoria">Obrigatória</option>
                        <option value="opcional">Opcional</option>
                        <option value="electiva">Electiva</option>
                        <option value="livre">Livre</option>

                    </select>

                    <select name="semester_number" class="border-gray-300 rounded-md">

                        <option value="">Semestre</option>

                        @for($semester = 1; $semester <= 4; $semester++) <option value="{{ $semester }}" @selected( (string) request('semester_number')===(string) $semester )>
                            {{ $semester }}º
                            </option>
                            @endfor

                    </select>

                    <button class="bg-gray-800 text-white rounded-md">
                        Filtrar
                    </button>

                </form>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-100">

                            <tr>
                                <th class="p-3 text-left">Código</th>
                                <th class="p-3 text-left">Disciplina</th>
                                <th class="p-3 text-left">Departamento</th>
                                <th class="p-3 text-left">Ano</th>
                                <th class="p-3 text-left">Semestre</th>
                                <th class="p-3 text-left">Créditos</th>
                                <th class="p-3 text-left">Horas</th>
                                <th class="p-3 text-left">Estado</th>
                                <th class="p-3 text-right">Acções</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($subjects as $subject)

                            <tr class="border-b">

                                <td class="p-3 font-medium">
                                    {{ $subject->code }}
                                </td>

                                <td class="p-3">
                                    {{ $subject->name }}
                                </td>

                                <td class="p-3">
                                    {{ $subject->department?->name ?? '-' }}
                                </td>

                                <td class="p-3">
                                    {{ $subject->year_number ? $subject->year_number . 'º' : '-' }}
                                </td>

                                <td class="p-3">
                                    {{ $subject->semester_number ? $subject->semester_number . 'º' : '-' }}
                                </td>

                                <td class="p-3">
                                    {{ $subject->credits }}
                                </td>

                                <td class="p-3">
                                    {{ $subject->total_hours }}h
                                </td>

                                <td class="p-3">

                                    @if($subject->is_active)
                                    <span class="text-green-700">
                                        Activa
                                    </span>
                                    @else
                                    <span class="text-red-700">
                                        Inactiva
                                    </span>
                                    @endif

                                </td>

                                <td class="p-3 text-right whitespace-nowrap">

                                    @can('subjects.update')

                                    <a href="{{ route('university.subjects.edit', $subject->id) }}" class="text-indigo-600 mr-2">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('university.subjects.status', $subject->id) }}" class="inline">

                                        @csrf
                                        @method('PATCH')

                                        <button class="text-orange-600 mr-2">
                                            {{ $subject->is_active
                                                        ? 'Desactivar'
                                                        : 'Activar' }}
                                        </button>

                                    </form>

                                    @endcan

                                    @can('subjects.delete')

                                    <form method="POST" action="{{ route('university.subjects.destroy', $subject->id) }}" class="inline" onsubmit="return confirm('Deseja eliminar esta disciplina?')">

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
                                <td colspan="9" class="p-5 text-center text-gray-500">
                                    Nenhuma disciplina encontrada.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-5">
                    {{ $subjects->links() }}
                </div>

            </div>

        </div>

    </div>

</x-app-layout>
