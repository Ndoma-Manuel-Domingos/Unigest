<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Gestão de Cursos
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
                        Cursos
                    </h3>

                    @can('courses.create')
                    <a href="{{ route('university.courses.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Novo Curso
                    </a>
                    @endcan

                </div>

                <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 mb-5">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar..." class="border-gray-300 rounded-md">

                    <select name="faculty_id" class="border-gray-300 rounded-md">

                        <option value="">Faculdades</option>

                        @foreach($faculties as $faculty)
                        <option value="{{ $faculty->id }}" @selected((string) request('faculty_id')===(string) $faculty->id)>
                            {{ $faculty->name }}
                        </option>
                        @endforeach

                    </select>

                    <select name="degree_type" class="border-gray-300 rounded-md">

                        <option value="">Graus</option>

                        @foreach([
                        'licenciatura' => 'Licenciatura',
                        'bacharelato' => 'Bacharelato',
                        'mestrado' => 'Mestrado',
                        'doutoramento' => 'Doutoramento',
                        'pos_graduacao' => 'Pós-graduação',
                        'curso_tecnico' => 'Curso Técnico',
                        'outro' => 'Outro',
                        ] as $value => $label)

                        <option value="{{ $value }}" @selected(request('degree_type')===$value)>
                            {{ $label }}
                        </option>

                        @endforeach

                    </select>

                    <select name="status" class="border-gray-300 rounded-md">

                        <option value="">Estados</option>
                        <option value="1" @selected(request('status')==='1' )>
                            Activos
                        </option>
                        <option value="0" @selected(request('status')==='0' )>
                            Inactivos
                        </option>

                    </select>

                    <button class="bg-gray-800 text-white rounded-md">
                        Filtrar
                    </button>

                </form>

                <div class="overflow-x-auto">

                    <table class="w-full text-sm">

                        <thead class="bg-gray-100">

                            <tr>
                                <th class="p-3 text-left">Curso</th>
                                <th class="p-3 text-left">Código</th>
                                <th class="p-3 text-left">Faculdade</th>
                                <th class="p-3 text-left">Departamento</th>
                                <th class="p-3 text-left">Grau</th>
                                <th class="p-3 text-left">Regime</th>
                                <th class="p-3 text-left">Estado</th>
                                <th class="p-3 text-right">Acções</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($courses as $course)

                            <tr class="border-b">

                                <td class="p-3 font-medium">
                                    {{ $course->name }}
                                </td>

                                <td class="p-3">
                                    {{ $course->code }}
                                </td>

                                <td class="p-3">
                                    {{ $course->faculty->name }}
                                </td>

                                <td class="p-3">
                                    {{ $course->department?->name ?? '-' }}
                                </td>

                                <td class="p-3">
                                    {{ ucfirst(str_replace('_', ' ', $course->degree_type)) }}
                                </td>

                                <td class="p-3">
                                    {{ ucfirst(str_replace('_', ' ', $course->study_regime)) }}
                                </td>

                                <td class="p-3">

                                    @if($course->is_active)
                                    <span class="text-green-700">
                                        Activo
                                    </span>
                                    @else
                                    <span class="text-red-700">
                                        Inactivo
                                    </span>
                                    @endif

                                </td>

                                <td class="p-3 text-right whitespace-nowrap">

                                    @can('courses.update')

                                    <a href="{{ route('university.courses.edit', $course->id) }}" class="text-indigo-600 mr-2">
                                        Editar
                                    </a>

                                    <form method="POST" action="{{ route('university.courses.status', $course->id) }}" class="inline">

                                        @csrf
                                        @method('PATCH')

                                        <button class="text-orange-600 mr-2">
                                            {{ $course->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>

                                    </form>

                                    @endcan

                                    @can('courses.delete')

                                    <form method="POST" action="{{ route('university.courses.destroy', $course->id) }}" class="inline" onsubmit="return confirm('Deseja eliminar este curso?')">

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
                                <td colspan="8" class="p-5 text-center text-gray-500">
                                    Nenhum curso encontrado.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-5">
                    {{ $courses->links() }}
                </div>

            </div>

        </div>

    </div>

</x-app-layout>
