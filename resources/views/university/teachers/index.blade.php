<x-app-layout>

    <x-slot name="header">

        <div class="flex justify-between items-center">

            <div>
                <h2 class="font-semibold text-xl text-gray-800">
                    Professores
                </h2>

                <p class="text-sm text-gray-500">
                    Gestão do corpo docente
                </p>
            </div>

            @can('teachers.create')
            <a href="{{ route('university.teachers.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                + Novo Professor
            </a>
            @endcan

        </div>

    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white shadow rounded-lg p-4 mb-6">

                <form method="GET" action="{{ route('university.teachers.index') }}">

                    <div class="flex gap-2">

                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, número, funcionário ou email" class="flex-1 rounded-lg border-gray-300">

                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg">
                            Pesquisar
                        </button>

                    </div>

                </form>

            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="bg-gray-50">

                            <tr>
                                <th class="px-4 py-3 text-left">Nº</th>
                                <th class="px-4 py-3 text-left">Professor</th>
                                <th class="px-4 py-3 text-left">Título</th>
                                <th class="px-4 py-3 text-left">Especialização</th>
                                <th class="px-4 py-3 text-left">Contacto</th>
                                <th class="px-4 py-3 text-left">Estado</th>
                                <th class="px-4 py-3 text-right">Acções</th>
                            </tr>

                        </thead>

                        <tbody class="divide-y">

                            @forelse($teachers as $teacher)

                            <tr>

                                <td class="px-4 py-3">
                                    {{ $teacher->teacher_number }}
                                </td>

                                <td class="px-4 py-3 font-medium">
                                    {{ $teacher->name }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $teacher->academic_title ?: '—' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $teacher->specialization ?: '—' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $teacher->phone ?: $teacher->email ?: '—' }}
                                </td>

                                <td class="px-4 py-3">

                                    @if($teacher->is_active)
                                    <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                        Activo
                                    </span>
                                    @else
                                    <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                        Inactivo
                                    </span>
                                    @endif

                                </td>

                                <td class="px-4 py-3 text-right">

                                    @can('teachers.update')
                                    <a href="{{ route('university.teachers.edit', $teacher) }}" class="text-blue-600 hover:underline mr-3">
                                        Editar
                                    </a>
                                    @endcan

                                    @can('teachers.delete')
                                    <form method="POST" action="{{ route('university.teachers.destroy', $teacher) }}" class="inline">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" onclick="return confirm('Eliminar este professor?')" class="text-red-600 hover:underline">
                                            Eliminar
                                        </button>

                                    </form>
                                    @endcan

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    Nenhum professor encontrado.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="p-4">
                    {{ $teachers->links() }}
                </div>

            </div>

        </div>

    </div>

</x-app-layout>
