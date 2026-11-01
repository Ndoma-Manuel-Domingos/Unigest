<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800">
                Gestão de Estudantes
            </h2>

            @can('students.create')
            <a href="{{ route('university.students.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                + Novo Estudante
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

            <div class="bg-white p-5 rounded-lg shadow mb-5">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, número, NIF, telefone ou email..." class="rounded-lg border-gray-300 md:col-span-2">

                    <select name="status" class="rounded-lg border-gray-300">
                        <option value="">Todos os estados</option>
                        <option value="active" @selected(request('status')==='active' )>
                            Activos
                        </option>
                        <option value="inactive" @selected(request('status')==='inactive' )>
                            Inactivos
                        </option>
                    </select>

                    <div class="md:col-span-3 flex gap-2">
                        <button class="bg-gray-800 text-white px-4 py-2 rounded-lg">
                            Pesquisar
                        </button>

                        <a href="{{ route('university.students.index') }}" class="bg-gray-200 px-4 py-2 rounded-lg">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-5 py-3 text-left">Nº Estudante</th>
                            <th class="px-5 py-3 text-left">Estudante</th>
                            <th class="px-5 py-3 text-left">NIF</th>
                            <th class="px-5 py-3 text-left">Contacto</th>
                            <th class="px-5 py-3 text-center">Matrículas</th>
                            <th class="px-5 py-3 text-center">Estado</th>
                            <th class="px-5 py-3 text-right">Acções</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($students as $student)
                        <tr>
                            <td class="px-5 py-4 font-semibold">
                                {{ $student->student_number }}
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($student->photo)
                                    <img src="{{ Storage::disk('public')->url($student->photo) }}" class="w-10 h-10 rounded-full object-cover">
                                    @else
                                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                    </div>
                                    @endif

                                    <div>
                                        <div class="font-medium">{{ $student->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $student->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4">{{ $student->nif ?: '—' }}</td>

                            <td class="px-5 py-4">{{ $student->phone ?: '—' }}</td>

                            <td class="px-5 py-4 text-center">
                                {{ $student->enrollments_count }}
                            </td>

                            <td class="px-5 py-4 text-center">
                                @if($student->is_active)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs">
                                    Activo
                                </span>
                                @else
                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs">
                                    Inactivo
                                </span>
                                @endif
                            </td>

                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                @can('students.view')
                                <a href="{{ route('university.students.show', $student) }}" class="text-blue-600 hover:underline mr-2">
                                    Ver
                                </a>
                                @endcan

                                @can('students.update')
                                <a href="{{ route('university.students.edit', $student) }}" class="text-indigo-600 hover:underline mr-2">
                                    Editar
                                </a>

                                <form method="POST" action="{{ route('university.students.status', $student) }}" class="inline">
                                    @csrf
                                    @method('PATCH')

                                    <button class="text-orange-600 hover:underline">
                                        {{ $student->is_active ? 'Desactivar' : 'Activar' }}
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">
                                Nenhum estudante encontrado.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $students->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
