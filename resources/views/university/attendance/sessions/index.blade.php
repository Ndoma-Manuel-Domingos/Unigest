<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">
                    Aulas e Frequência
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $academicClass->name }}
                </p>
            </div>

            @can('attendance.view')
            <a href="{{ route('university.attendance.sessions.index', $academicClass) }}" class="text-blue-600 hover:underline">
                Aulas e Frequência
            </a>
            @endcan
            @can('attendance.create')
            <a href="{{ route('university.attendance.sessions.create', $academicClass) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                + Nova Aula
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

            <div class="bg-white shadow rounded-lg overflow-hidden">

                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Data</th>
                            <th class="px-4 py-3 text-left">Disciplina</th>
                            <th class="px-4 py-3 text-left">Tema</th>
                            <th class="px-4 py-3 text-left">Horário</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-right">Ações</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse($sessions as $session)

                        <tr>
                            <td class="px-4 py-3">
                                {{ $session->session_date?->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $session->curriculumSubject->subject->name }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $session->topic ?: '—' }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $session->start_time ?: '—' }}
                                @if($session->end_time)
                                - {{ $session->end_time }}
                                @endif
                            </td>

                            <td class="px-4 py-3">
                                @if($session->status === 'completed')
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">
                                    Concluída
                                </span>
                                @elseif($session->status === 'cancelled')
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-800">
                                    Cancelada
                                </span>
                                @else
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-800">
                                    Agendada
                                </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right space-x-2">

                                @if($session->status !== 'cancelled')
                                @can('attendance.view')
                                <a href="{{ route('university.attendance.show', $session) }}" class="text-blue-600 hover:underline">
                                    Frequência
                                </a>
                                @endcan
                                @endif

                                @can('attendance.update')
                                <a href="{{ route('university.attendance.sessions.edit', [
                                            $academicClass,
                                            $session
                                        ]) }}" class="text-gray-600 hover:underline">
                                    Editar
                                </a>
                                @endcan

                                @can('attendance.delete')
                                <form method="POST" action="{{ route('university.attendance.sessions.destroy', [
                                                  $academicClass,
                                                  $session
                                              ]) }}" class="inline">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" onclick="return confirm('Eliminar esta aula?')" class="text-red-600 hover:underline">
                                        Eliminar
                                    </button>
                                </form>
                                @endcan

                            </td>
                        </tr>

                        @empty

                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                Nenhuma aula registada.
                            </td>
                        </tr>

                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $sessions->links() }}
                </div>

            </div>

        </div>
    </div>

</x-app-layout>
