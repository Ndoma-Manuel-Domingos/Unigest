<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Planos Curriculares
                </h2>

                <p class="text-sm text-gray-500">
                    Gestão dos currículos dos cursos da universidade.
                </p>
            </div>

            @can('curricula.create')
            <a href="{{ route('university.curricula.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                + Novo Currículo
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="mb-6 rounded-lg bg-white p-5 shadow">

                <form method="GET" action="{{ route('university.curricula.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">

                    <div>
                        <label class="text-sm font-medium text-gray-700">
                            Pesquisa
                        </label>

                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, versão ou curso..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">
                            Curso
                        </label>

                        <select name="course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Todos os cursos</option>

                            @foreach($courses as $course)
                            <option value="{{ $course->id }}" @selected(request('course_id')==$course->id)
                                >
                                {{ $course->code }} — {{ $course->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">
                            Estado
                        </label>

                        <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Todos</option>
                            <option value="active" @selected(request('status')==='active' )>
                                Activos
                            </option>
                            <option value="inactive" @selected(request('status')==='inactive' )>
                                Inactivos
                            </option>
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">
                            Filtrar
                        </button>

                        <a href="{{ route('university.curricula.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">
                            Limpar
                        </a>
                    </div>

                </form>

            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow">

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Currículo
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Curso
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Versão
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Disciplinas
                                </th>

                                <th class="px-6 py-3 text-center text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Estado
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                    Acções
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">

                            @forelse($curricula as $curriculum)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">
                                        {{ $curriculum->name }}
                                    </div>

                                    @if($curriculum->effective_from)
                                    <div class="text-xs text-gray-500">
                                        Desde {{ $curriculum->effective_from->format('d/m/Y') }}
                                    </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    <div class="font-medium">
                                        {{ $curriculum->course->name }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $curriculum->course->code }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $curriculum->version ?: '—' }}
                                </td>

                                <td class="px-6 py-4 text-center text-sm font-semibold text-gray-700">
                                    {{ $curriculum->subjects_count }}
                                </td>

                                <td class="px-6 py-4 text-center">
                                    @if($curriculum->is_active)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Activo
                                    </span>
                                    @else
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Inactivo
                                    </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">

                                        @can('curricula.update')
                                        <a href="{{ route('university.curricula.edit', $curriculum) }}" class="rounded-md bg-indigo-100 px-3 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-200">
                                            Editar
                                        </a>
                                        @endcan

                                        @can('curricula.update')
                                        <form method="POST" action="{{ route('university.curricula.status', $curriculum) }}">
                                            @csrf
                                            @method('PATCH')

                                            <input type="hidden" name="is_active" value="{{ $curriculum->is_active ? 0 : 1 }}">

                                            <button type="submit" class="rounded-md bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200">
                                                {{ $curriculum->is_active ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>
                                        @endcan

                                        @can('curricula.delete')
                                        @if($curriculum->subjects_count === 0)
                                        <form method="POST" action="{{ route('university.curricula.destroy', $curriculum) }}" onsubmit="return confirm('Tem certeza que deseja eliminar este currículo?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="rounded-md bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200">
                                                Eliminar
                                            </button>
                                        </form>
                                        @endif
                                        @endcan

                                    </div>
                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">
                                    Nenhum currículo encontrado.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if($curricula->hasPages())
                <div class="border-t border-gray-200 px-6 py-4">
                    {{ $curricula->links() }}
                </div>
                @endif

            </div>

        </div>
    </div>

</x-app-layout>
