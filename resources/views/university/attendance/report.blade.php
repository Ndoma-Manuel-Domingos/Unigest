<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Relatório de Frequência
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6 mb-6">

                <h3 class="text-lg font-semibold">
                    {{ $subjectEnrollment->enrollment->student->name }}
                </h3>

                <p class="text-gray-500">
                    {{ $subjectEnrollment->enrollment->student->student_number }}
                </p>

                <p class="mt-2">
                    Disciplina:
                    <strong>
                        {{ $subjectEnrollment->curriculumSubject->subject->name }}
                    </strong>
                </p>

            </div>

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">

                <div class="bg-white shadow rounded-lg p-4">
                    <div class="text-sm text-gray-500">Total</div>
                    <div class="text-2xl font-bold">{{ $total }}</div>
                </div>

                <div class="bg-white shadow rounded-lg p-4">
                    <div class="text-sm text-gray-500">Presenças</div>
                    <div class="text-2xl font-bold text-green-600">
                        {{ $present }}
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-4">
                    <div class="text-sm text-gray-500">Atrasos</div>
                    <div class="text-2xl font-bold text-yellow-600">
                        {{ $late }}
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-4">
                    <div class="text-sm text-gray-500">Faltas</div>
                    <div class="text-2xl font-bold text-red-600">
                        {{ $absent }}
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-4">
                    <div class="text-sm text-gray-500">
                        Assiduidade
                    </div>

                    <div class="text-2xl font-bold">
                        {{ number_format($percentage, 2, ',', '.') }}%
                    </div>
                </div>

            </div>

            <div class="bg-white shadow rounded-lg overflow-hidden">

                <table class="min-w-full">

                    <thead class="bg-gray-50">

                        <tr>
                            <th class="px-4 py-3 text-left">
                                Data
                            </th>

                            <th class="px-4 py-3 text-left">
                                Tema
                            </th>

                            <th class="px-4 py-3 text-left">
                                Estado
                            </th>

                            <th class="px-4 py-3 text-left">
                                Observação
                            </th>
                        </tr>

                    </thead>

                    <tbody class="divide-y">

                        @forelse($records as $record)

                        <tr>

                            <td class="px-4 py-3">
                                {{ $record->classSession->session_date?->format('d/m/Y') }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $record->classSession->topic ?: '—' }}
                            </td>

                            <td class="px-4 py-3">

                                @switch($record->status)

                                @case('present')
                                <span class="text-green-600">
                                    Presente
                                </span>
                                @break

                                @case('late')
                                <span class="text-yellow-600">
                                    Atrasado
                                </span>
                                @break

                                @case('absent')
                                <span class="text-red-600">
                                    Falta
                                </span>
                                @break

                                @case('excused')
                                <span class="text-blue-600">
                                    Justificada
                                </span>
                                @break

                                @endswitch

                            </td>

                            <td class="px-4 py-3">
                                {{ $record->justification ?: '—' }}
                            </td>

                        </tr>

                        @empty

                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                Nenhum registo de frequência.
                            </td>
                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</x-app-layout>
