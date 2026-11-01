<x-app-layout>

    <x-slot name="header">

        <div class="flex justify-between items-center">

            <div>
                <h2 class="font-semibold text-xl text-gray-800">
                    Horário da Turma
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $academicClass->name }}
                </p>
            </div>

            @can('schedules.create')
            <a href="{{ route('university.schedules.create', $academicClass) }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                + Novo Horário
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

                <div class="overflow-x-auto">

                    <table class="min-w-full border-collapse">

                        <thead>

                            <tr class="bg-gray-800 text-white">

                                <th class="p-3 border w-32">
                                    Horário
                                </th>

                                <th class="p-3 border">
                                    Segunda
                                </th>

                                <th class="p-3 border">
                                    Terça
                                </th>

                                <th class="p-3 border">
                                    Quarta
                                </th>

                                <th class="p-3 border">
                                    Quinta
                                </th>

                                <th class="p-3 border">
                                    Sexta
                                </th>

                                <th class="p-3 border">
                                    Sábado
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @php
                            $slots = [];

                            foreach ($schedules as $schedule) {
                            $key = $schedule->start_time . '|' . $schedule->end_time;

                            $slots[$key] = [
                            'start' => $schedule->start_time,
                            'end' => $schedule->end_time,
                            ];
                            }

                            uasort($slots, function ($a, $b) {
                            return strcmp($a['start'], $b['start']);
                            });
                            @endphp

                            @forelse($slots as $slot)

                            <tr>

                                <td class="p-3 border bg-gray-50 font-semibold text-center whitespace-nowrap">
                                    {{ $slot['start'] }}
                                    <br>
                                    <span class="text-xs text-gray-500">
                                        {{ $slot['end'] }}
                                    </span>
                                </td>

                                @for($day = 1; $day <= 6; $day++) @php $daySchedules=$schedules ->where('day_of_week', $day)
                                    ->where('start_time', $slot['start'])
                                    ->where('end_time', $slot['end']);
                                    @endphp

                                    <td class="p-2 border align-top min-w-[180px]">

                                        @foreach($daySchedules as $schedule)

                                        <div class="rounded-lg bg-blue-50 border border-blue-200 p-3 mb-2">

                                            <div class="font-semibold text-blue-900">
                                                {{ $schedule->curriculumSubject->subject->name }}
                                            </div>

                                            <div class="text-xs text-gray-600 mt-1">
                                                {{ $schedule->teacher->name }}
                                            </div>

                                            @if($schedule->room)
                                            <div class="text-xs text-gray-600">
                                                Sala: {{ $schedule->room->name }}
                                            </div>
                                            @endif

                                            @can('schedules.update')

                                            <a href="{{ route('university.schedules.edit', $schedule) }}" class="inline-block mt-2 text-xs text-blue-600 hover:underline">

                                                Editar

                                            </a>

                                            @endcan

                                        </div>

                                        @endforeach

                                    </td>

                                    @endfor

                            </tr>

                            @empty

                            <tr>
                                <td colspan="7" class="p-10 text-center text-gray-500">

                                    Nenhum horário definido para esta turma.

                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

            @if($schedules->count())

            <div class="mt-6 bg-white shadow rounded-lg overflow-hidden">

                <div class="p-4 border-b">
                    <h3 class="font-semibold">
                        Lista de Horários
                    </h3>
                </div>

                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="bg-gray-50">

                            <tr>
                                <th class="px-4 py-3 text-left">
                                    Dia
                                </th>

                                <th class="px-4 py-3 text-left">
                                    Horário
                                </th>

                                <th class="px-4 py-3 text-left">
                                    Disciplina
                                </th>

                                <th class="px-4 py-3 text-left">
                                    Professor
                                </th>

                                <th class="px-4 py-3 text-left">
                                    Sala
                                </th>

                                <th class="px-4 py-3 text-right">
                                    Acções
                                </th>
                            </tr>

                        </thead>

                        <tbody class="divide-y">

                            @foreach($schedules as $schedule)

                            <tr>

                                <td class="px-4 py-3">
                                    {{ $schedule->day_name }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $schedule->start_time }}
                                    -
                                    {{ $schedule->end_time }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $schedule->curriculumSubject->subject->name }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $schedule->teacher->name }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $schedule->room?->name ?: '—' }}
                                </td>

                                <td class="px-4 py-3 text-right">

                                    @can('schedules.update')
                                    <a href="{{ route('university.schedules.edit', $schedule) }}" class="text-blue-600 mr-3">
                                        Editar
                                    </a>
                                    @endcan

                                    @can('schedules.delete')

                                    <form method="POST" action="{{ route('university.schedules.destroy', $schedule) }}" class="inline">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" onclick="return confirm('Eliminar este horário?')" class="text-red-600">
                                            Eliminar
                                        </button>

                                    </form>

                                    @endcan

                                </td>

                            </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

            @endif

        </div>

    </div>

</x-app-layout>
