<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800">
                Lançamento de notas
            </h2>
            <p class="text-sm text-gray-500">
                {{ $assessment->title }} —
                {{ $assessment->curriculumSubject->subject->name }}
            </p>
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

            @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-5 mb-5">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Turma</p>
                        <p class="font-semibold">{{ $assessment->academicClass->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ponderação</p>
                        <p class="font-semibold">{{ $assessment->weight }}%</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Nota máxima</p>
                        <p class="font-semibold">{{ $assessment->max_grade }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Data</p>
                        <p class="font-semibold">{{ $assessment->assessment_date->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-5">
                @if(!$assessment->is_published)
                <form method="POST" action="{{ route('university.assessments.save-grades', $assessment) }}">
                    @csrf
                    @method('PUT')

                    <div class="flex flex-wrap justify-between items-center gap-3 mb-5">
                        <h3 class="font-semibold text-lg">Estudantes inscritos</h3>
                        <button class="px-5 py-2 bg-indigo-600 text-white rounded-md">
                            Guardar notas
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">N.º estudante</th>
                                    <th class="px-4 py-3 text-left">Nome</th>
                                    <th class="px-4 py-3 text-left">Nota / {{ $assessment->max_grade }}</th>
                                    <th class="px-4 py-3 text-left">Observações</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200">
                                @forelse($students as $index => $item)
                                @php
                                $grade = $gradesByEnrollment->get($item->id);
                                @endphp

                                <tr>
                                    <td class="px-4 py-3">
                                        {{ $item->enrollment->student->student_number }}
                                    </td>
                                    <td class="px-4 py-3 font-medium">
                                        {{ $item->enrollment->student->name }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="hidden" name="grades[{{ $index }}][subject_enrollment_id]" value="{{ $item->id }}">

                                        <input type="number" name="grades[{{ $index }}][grade]" min="0" max="{{ $assessment->max_grade }}" step="0.01" value="{{ old("grades.$index.grade", $grade?->grade) }}" class="w-28 rounded-md border-gray-300" placeholder="Nota">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" name="grades[{{ $index }}][observations]" value="{{ old("grades.$index.observations", $grade?->observations) }}" class="w-full rounded-md border-gray-300" placeholder="Observação">
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                        Não existem estudantes inscritos nesta disciplina e turma.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </form>
                @else
                <div class="p-4 bg-green-50 text-green-800 rounded">
                    Esta avaliação foi publicada. As notas estão bloqueadas para edição.
                </div>

                <table class="min-w-full mt-5 divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Estudante</th>
                            <th class="px-4 py-3 text-left">Nota</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assessment->grades as $grade)
                        <tr>
                            <td class="px-4 py-3">
                                {{ $grade->subjectEnrollment->enrollment->student->name }}
                            </td>
                            <td class="px-4 py-3">{{ $grade->grade ?? '—' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif

                @can('assessments.publish')
                @if(!$assessment->is_published)
                <form method="POST" action="{{ route('university.assessments.publish', $assessment) }}" class="mt-6 border-t pt-5" onsubmit="return confirm('Depois da publicação, as notas desta avaliação ficarão bloqueadas. Continuar?')">
                    @csrf
                    @method('PATCH')

                    <button class="px-5 py-2 bg-green-700 text-white rounded-md">
                        Publicar avaliação
                    </button>
                </form>
                @endif
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
