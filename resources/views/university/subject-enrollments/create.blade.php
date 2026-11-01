<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800">
                Inscrever disciplinas
            </h2>
            <p class="text-sm text-gray-500">
                {{ $enrollment->student->name }}
                — {{ $enrollment->enrollment_number }}
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                <strong>Não foi possível concluir a inscrição:</strong>
                <ul class="list-disc ml-5 mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-5 mb-5">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Curso</p>
                        <p class="font-semibold">{{ $enrollment->course->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Plano curricular</p>
                        <p class="font-semibold">{{ $enrollment->curriculum->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ano / Semestre</p>
                        <p class="font-semibold">
                            {{ $enrollment->year_number }}.º ano
                            @if($enrollment->semester_number)
                            — {{ $enrollment->semester_number }}.º semestre
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('university.subject-enrollments.store', $enrollment) }}" class="bg-white rounded-lg shadow-sm p-5">
                @csrf

                <div class="flex flex-wrap justify-between items-center gap-3 mb-5">
                    <h3 class="font-semibold text-lg">Disciplinas disponíveis</h3>

                    <label class="flex items-center gap-2 text-sm">
                        <input type="checkbox" id="select-all" class="rounded">
                        Seleccionar todas as disponíveis
                    </label>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium mb-1">
                        Data da inscrição *
                    </label>
                    <input type="date" name="enrollment_date" required value="{{ old('enrollment_date', date('Y-m-d')) }}" class="rounded-md border-gray-300">
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-3 py-3 text-left">Seleccionar</th>
                                <th class="px-3 py-3 text-left">Código</th>
                                <th class="px-3 py-3 text-left">Disciplina</th>
                                <th class="px-3 py-3 text-left">Créditos</th>
                                <th class="px-3 py-3 text-left">Pré-requisitos</th>
                                <th class="px-3 py-3 text-left">Situação</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($curriculumSubjects as $item)
                            @php
                            $already = in_array($item->id, $alreadyRegistered);
                            $completed = in_array($item->id, $completedSubjects);

                            $missing = $item->prerequisites->filter(function ($prerequisite) use ($completedSubjects) {
                            return !in_array($prerequisite->id, $completedSubjects);
                            });

                            $blocked = $missing->isNotEmpty();
                            @endphp

                            <tr class="{{ $already || $completed || $blocked ? 'bg-gray-50' : '' }}">
                                <td class="px-3 py-3">
                                    @if(!$already && !$completed && !$blocked)
                                    <input type="checkbox" name="curriculum_subject_ids[]" value="{{ $item->id }}" class="subject-checkbox rounded">
                                    @else
                                    <span class="text-gray-400">—</span>
                                    @endif
                                </td>

                                <td class="px-3 py-3">
                                    {{ $item->subject->code }}
                                </td>

                                <td class="px-3 py-3 font-medium">
                                    {{ $item->subject->name }}
                                </td>

                                <td class="px-3 py-3">
                                    {{ $item->credits ?? $item->subject->credits ?? '—' }}
                                </td>

                                <td class="px-3 py-3">
                                    @forelse($item->prerequisites as $prerequisite)
                                    <div class="text-sm">
                                        {{ $prerequisite->subject->name }}
                                    </div>
                                    @empty
                                    <span class="text-gray-400">Sem pré-requisitos</span>
                                    @endforelse
                                </td>

                                <td class="px-3 py-3">
                                    @if($already)
                                    <span class="text-blue-700 text-sm">Já inscrita</span>
                                    @elseif($completed)
                                    <span class="text-green-700 text-sm">Já concluída</span>
                                    @elseif($blocked)
                                    <span class="text-red-700 text-sm">
                                        Pré-requisito em falta
                                    </span>
                                    @else
                                    <span class="text-green-700 text-sm">Disponível</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-3 py-8 text-center text-gray-500">
                                    Não existem disciplinas activas para este ano e semestre no plano curricular.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end gap-3 mt-6 border-t pt-5">
                    <a href="{{ route('university.subject-enrollments.index', $enrollment) }}" class="px-4 py-2 border rounded-md">
                        Cancelar
                    </a>

                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md">
                        Inscrever disciplinas seleccionadas
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.subject-checkbox');

            selectAll.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    selectAll.checked =
                        checkboxes.length > 0 &&
                        Array.from(checkboxes).every(item => item.checked);
                });
            });
        });

    </script>
</x-app-layout>
