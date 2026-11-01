<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">
                    Salas
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Gestão das salas e espaços da universidade
                </p>
            </div>

            @can('rooms.create')
            <a href="{{ route('university.rooms.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                + Nova Sala
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 rounded-lg bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 rounded-lg bg-red-100 p-4 text-red-800">
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-4 mb-6">

                <form method="GET" action="{{ route('university.rooms.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div>
                        <label class="text-sm font-medium text-gray-700">
                            Pesquisa
                        </label>

                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, código ou edifício" class="mt-1 w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="text-sm font-medium text-gray-700">
                            Estado
                        </label>

                        <select name="status" class="mt-1 w-full rounded-lg border-gray-300">

                            <option value="">Todos</option>

                            <option value="active" @selected(request('status')==='active' )}>
                                Activas
                            </option>

                            <option value="inactive" @selected(request('status')==='inactive' )}>
                                Inactivas
                            </option>

                        </select>
                    </div>

                    <div class="flex items-end gap-2">

                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white rounded-lg">
                            Pesquisar
                        </button>

                        <a href="{{ route('university.rooms.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">
                            Limpar
                        </a>

                    </div>

                </form>

            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="bg-gray-50">

                            <tr>
                                <th class="px-4 py-3 text-left">Sala</th>
                                <th class="px-4 py-3 text-left">Código</th>
                                <th class="px-4 py-3 text-left">Localização</th>
                                <th class="px-4 py-3 text-left">Tipo</th>
                                <th class="px-4 py-3 text-left">Capacidade</th>
                                <th class="px-4 py-3 text-left">Estado</th>
                                <th class="px-4 py-3 text-right">Acções</th>
                            </tr>

                        </thead>

                        <tbody class="divide-y">

                            @forelse($rooms as $room)

                            <tr class="hover:bg-gray-50">

                                <td class="px-4 py-3 font-medium">
                                    {{ $room->name }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $room->code ?: '—' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $room->building ?: '—' }}

                                    @if($room->floor)
                                    / Piso {{ $room->floor }}
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    @switch($room->type)
                                    @case('classroom')
                                    Sala de Aula
                                    @break

                                    @case('laboratory')
                                    Laboratório
                                    @break

                                    @case('auditorium')
                                    Auditório
                                    @break

                                    @case('office')
                                    Gabinete
                                    @break

                                    @default
                                    Outro
                                    @endswitch
                                </td>

                                <td class="px-4 py-3">
                                    {{ $room->capacity ?: '—' }}
                                </td>

                                <td class="px-4 py-3">

                                    @if($room->is_active)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        Activa
                                    </span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                        Inactiva
                                    </span>
                                    @endif

                                </td>

                                <td class="px-4 py-3 text-right">

                                    @can('rooms.update')
                                    <a href="{{ route('university.rooms.edit', $room) }}" class="text-blue-600 hover:underline mr-3">
                                        Editar
                                    </a>
                                    @endcan

                                    @can('rooms.delete')
                                    <form method="POST" action="{{ route('university.rooms.destroy', $room) }}" class="inline">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" onclick="return confirm('Eliminar esta sala?')" class="text-red-600 hover:underline">
                                            Eliminar
                                        </button>

                                    </form>
                                    @endcan

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    Nenhuma sala encontrada.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="p-4">
                    {{ $rooms->links() }}
                </div>

            </div>

        </div>

    </div>

</x-app-layout>
