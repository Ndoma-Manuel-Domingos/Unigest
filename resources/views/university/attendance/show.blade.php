<x-app-layout>

    <x-slot name="header">

        <div>
            <h2 class="font-semibold text-xl text-gray-800">
                Registo de Frequência
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                {{ $session->curriculumSubject->subject->name }}
                —
                {{ $session->session_date?->format('d/m/Y') }}
            </p>
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

            <div class="bg-white shadow rounded-lg p-6 mb-6">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    <div>
                        <span class="text-sm text-gray-500">
                            Turma
                        </span>

                        <div class="font-semibold">
                            {{ $session->academicClass->name }}
                        </div>
                    </div>

                    <div>
                        <span class="text-sm text-gray-500">
                            Disciplina
                        </span>

                        <div class="font-semibold">
                            {{ $session->curriculumSubject->subject->name }}
                        </div>
                    </div>

                    <div>
                        <span class="text-sm text-gray-500">
                            Data
                        </span>

                        <div class="font-semibold">
                            {{ $session->session_date?->format('d/m/Y') }}
                        </div>
                    </div>

                    <div>
                        <span class="text-sm text-gray-500">
                            Tema
                        </span>

                        <div class="font-semibold">
                            {{ $session->topic ?: '—' }}
                        </div>
                    </div>

                </div>

            </div>

            <form method="POST" action="{{ route('university.attendance.save', $session) }}">

                @csrf

                <div class="bg-white shadow rounded-lg overflow-hidden">

                    <div class="p-4 border-b flex justify-between items-center">

                        <h3 class="font-semibold">
                            Lista de Estudantes
                        </h3>

                        @can('attendance.register')
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Guardar Frequência
                        </button>
                        @endcan

                    </div>

                    <div class="overflow-x-auto">

                        <table class="min-w-full">

                            <thead class="bg-gray-50">

                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        Nº
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Estudante
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Estado
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Hora chegada
                                    </th>

                                    <th class="px-4 py-3 text-left">
                                        Justificação
                                    </th>
                                </tr>

                            </thead>

                            <tbody class="divide-y">

                                @forelse($subjectEnrollments as $subjectEnrollment)

                                @php
                                $record = $records->get($subjectEnrollment->id);
                                @endphp

                                <tr>

                                    <td class="px-4 py-3">
                                        {{ $subjectEnrollment->enrollment->student->student_number }}
                                    </td>

                                    <td class="px-4 py-3 font-medium">
                                        {{ $subjectEnrollment->enrollment->student->name }}
                                    </td>

                                    <td class="px-4 py-3">

                                        <select name="attendance[{{ $subjectEnrollment->id }}][status]" class="attendance-status rounded-lg border-gray-300 w-full" data-id="{{ $subjectEnrollment->id }}">

                                            <option value="present" @selected(($record?->status ?? 'present') === 'present')>
                                                Presente
                                            </option>

                                            <option value="absent" @selected($record?->status === 'absent')>
                                                Falta
                                            </option>

                                            <option value="late" @selected($record?->status === 'late')>
                                                Atrasado
                                            </option>

                                            <option value="excused" @selected($record?->status === 'excused')>
                                                Falta Justificada
                                            </option>

                                        </select>

                                    </td>

                                    <td class="px-4 py-3">

                                        <input type="time" name="attendance[{{ $subjectEnrollment->id }}][arrival_time]" value="{{ $record?->arrival_time }}" class="rounded-lg border-gray-300 w-full">

                                    </td>

                                    <td class="px-4 py-3">

                                        <input type="text" name="attendance[{{ $subjectEnrollment->id }}][justification]" value="{{ $record?->justification }}" class="rounded-lg border-gray-300 w-full" placeholder="Justificação">

                                    </td>

                                </tr>

                                @empty

                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        Nenhum estudante inscrito nesta disciplina.
                                    </td>
                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
