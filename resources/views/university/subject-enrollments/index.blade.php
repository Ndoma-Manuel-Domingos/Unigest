<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">
                    Disciplinas inscritas
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $enrollment->student->name }}
                    — {{ $enrollment->enrollment_number }}
                </p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('university.enrollments.show', $enrollment) }}" class="px-4 py-2 border rounded-md">
                    Matrícula
                </a>

                @can('subject_enrollments.create')
                <a href="{{ route('university.subject-enrollments.create', $enrollment) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">
                    Inscrever disciplinas
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-5 mb-5">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Curso</p>
                        <p class="font-medium">{{ $enrollment->course->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ano académico</p>
                        <p class="font-medium">{{ $enrollment->academicYear->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ano curricular</p>
                        <p class="font-medium">{{ $enrollment->year_number }}.º ano</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Semestre</p>
                        <p class="font-medium">
                            {{ $enrollment->semester_number
                                ? $enrollment->semester_number . '.º semestre'
                                : 'Todos' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Código</th>
                            <th class="px-4 py-3 text-left">Disciplina</th>
                            <th class="px-4 py-3 text-left">Créditos</th>
                            <th class="px-4 py-3 text-left">Data</th>
                            <th class="px-4 py-3 text-left">Nota</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-right">Acções</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($subjects as $item)
                        @php
                        $subject = $item->curriculumSubject->subject;
                        $statuses = [
                        'enrolled' => 'Inscrita',
                        'approved' => 'Aprovada',
                        'failed' => 'Reprovada',
                        'withdrawn' => 'Desistente',
                        ];
                        @endphp

                        <tr>
                            <td class="px-4 py-3">{{ $subject->code }}</td>
                            <td class="px-4 py-3 font-medium">{{ $subject->name }}</td>
                            <td class="px-4 py-3">
                                {{ $item->curriculumSubject->credits ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $item->enrollment_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $item->final_grade ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $statuses[$item->status] ?? $item->status }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @can('subject_enrollments.update')
                                <details class="inline-block text-left">
                                    <summary class="cursor-pointer text-indigo-600">
                                        Gerir
                                    </summary>

                                    <form method="POST" action="{{ route('university.subject-enrollments.update-status', [$enrollment, $item]) }}" class="mt-2 p-3 border rounded bg-white w-64">
                                        @csrf
                                        @method('PATCH')

                                        <label class="block text-sm mb-1">Estado</label>
                                        <select name="status" required class="w-full rounded border-gray-300 mb-2">
                                            <option value="enrolled" @selected($item->status === 'enrolled')>Inscrita</option>
                                            <option value="approved" @selected($item->status === 'approved')>Aprovada</option>
                                            <option value="failed" @selected($item->status === 'failed')>Reprovada</option>
                                            <option value="withdrawn" @selected($item->status === 'withdrawn')>Desistente</option>
                                        </select>

                                        <label class="block text-sm mb-1">Nota final (0–20)</label>
                                        <input type="number" name="final_grade" min="0" max="20" step="0.01" value="{{ $item->final_grade }}" class="w-full rounded border-gray-300 mb-2">

                                        <label class="block text-sm mb-1">Observações</label>
                                        <textarea name="observations" rows="2" class="w-full rounded border-gray-300 mb-2">{{ $item->observations }}</textarea>

                                        <button class="w-full bg-indigo-600 text-white py-2 rounded">
                                            Guardar
                                        </button>
                                    </form>

                                    @if($item->status !== 'approved')
                                    <form method="POST" action="{{ route('university.subject-enrollments.destroy', [$enrollment, $item]) }}" onsubmit="return confirm('Queres remover esta inscrição?')" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 text-sm">
                                            Remover inscrição
                                        </button>
                                    </form>
                                    @endif
                                </details>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Ainda não existem disciplinas inscritas nesta matrícula.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
