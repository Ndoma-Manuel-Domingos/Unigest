<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800">
                Gestão de Turmas e Classes
            </h2>

            @can('classes.create')
            <a href="{{ route('university.classes.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                + Nova Turma
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 bg-red-100 text-red-800 p-3 rounded-lg">
                {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-4 bg-red-100 text-red-800 p-3 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white p-5 rounded-lg shadow mb-5">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome ou código..." class="rounded-lg border-gray-300">

                    <select name="academic_year_id" class="rounded-lg border-gray-300">
                        <option value="">Todos os anos lectivos</option>
                        @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" @selected(request('academic_year_id')==$year->id)>
                            {{ $year->name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="course_id" class="rounded-lg border-gray-300">
                        <option value="">Todos os cursos</option>
                        @foreach($courses as $course)
                        <option value="{{ $course->id }}" @selected(request('course_id')==$course->id)>
                            {{ $course->name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="shift" class="rounded-lg border-gray-300">
                        <option value="">Todos os turnos</option>
                        @foreach([
                        'manha' => 'Manhã',
                        'tarde' => 'Tarde',
                        'noite' => 'Noite',
                        'integral' => 'Integral',
                        'pos_laboral' => 'Pós-laboral',
                        'outro' => 'Outro'
                        ] as $value => $label)
                        <option value="{{ $value }}" @selected(request('shift')===$value)>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>

                    <select name="status" class="rounded-lg border-gray-300">
                        <option value="">Todos os estados</option>
                        <option value="active" @selected(request('status')==='active' )>Activas</option>
                        <option value="inactive" @selected(request('status')==='inactive' )>Inactivas</option>
                    </select>

                    <div class="md:col-span-5 flex gap-2">
                        <button class="bg-gray-800 text-white px-4 py-2 rounded-lg">
                            Filtrar
                        </button>

                        <a href="{{ route('university.classes.index') }}" class="bg-gray-200 px-4 py-2 rounded-lg">
                            Limpar filtros
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left">Código</th>
                            <th class="px-5 py-3 text-left">Turma</th>
                            <th class="px-5 py-3 text-left">Curso</th>
                            <th class="px-5 py-3 text-left">Ano Lectivo</th>
                            <th class="px-5 py-3 text-center">Ano/Semestre</th>
                            <th class="px-5 py-3 text-center">Turno</th>
                            <th class="px-5 py-3 text-center">Capacidade</th>
                            <th class="px-5 py-3 text-center">Estado</th>
                            <th class="px-5 py-3 text-right">Acções</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($classes as $class)
                        <tr>
                            <td class="px-5 py-4 font-medium">
                                {{ $class->code }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $class->name }}
                                @if($class->curriculum)
                                <div class="text-xs text-gray-500">
                                    {{ $class->curriculum->name }}
                                </div>
                                @endif
                            </td>

                            <td class="px-5 py-4">
                                {{ $class->course->name }}
                            </td>

                            <td class="px-5 py-4">
                                {{ $class->academicYear->name }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                {{ $class->year_number }}º /
                                {{ $class->semester_number }}º
                            </td>

                            <td class="px-5 py-4 text-center">
                                {{ ucfirst(str_replace('_', '-', $class->shift)) }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                {{ $class->capacity }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                @if($class->is_active)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                    Activa
                                </span>
                                @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">
                                    Inactiva
                                </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                @can('classes.update')
                                <a href="{{ route('university.classes.edit', $class) }}" class="text-indigo-600 hover:underline mr-2">
                                    Editar
                                </a>

                                <form method="POST" action="{{ route('university.classes.status', $class) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-orange-600 hover:underline">
                                        {{ $class->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                                @endcan

                                @can('schedules.view')
                                <a href="{{ route('university.schedules.index', $class) }}" class="text-blue-600 hover:underline">
                                    Horário
                                </a>
                                @endcan

                                @can('attendance.view')
                                <a href="{{ route('university.attendance.sessions.index', $class) }}" class="text-green-600 hover:underline">
                                    Frequência
                                </a>
                                @endcan

                                @can('classes.delete')
                                <form method="POST" action="{{ route('university.classes.destroy', $class) }}" class="inline" onsubmit="return confirm('Confirma a eliminação desta turma?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline ml-2">
                                        Eliminar
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-500">
                                Nenhuma turma encontrada.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $classes->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
