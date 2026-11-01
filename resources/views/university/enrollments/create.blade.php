<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Registar matrícula</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                <strong>Verifica os seguintes erros:</strong>
                <ul class="list-disc ml-5 mt-2">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('university.enrollments.store') }}" class="bg-white shadow-sm rounded-lg p-6 space-y-6">
                @csrf

                <div>
                    <h3 class="font-semibold text-lg mb-4">Dados do estudante</h3>

                    <label class="block text-sm font-medium mb-1">Estudante *</label>
                    <select name="student_id" required class="w-full rounded-md border-gray-300">
                        <option value="">Seleccione o estudante</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}" @selected(old('student_id')==$student->id)>
                            {{ $student->student_number }} — {{ $student->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="border-t pt-5">
                    <h3 class="font-semibold text-lg mb-4">Dados académicos</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Ano académico *</label>
                            <select name="academic_year_id" id="academic_year_id" required class="w-full rounded-md border-gray-300">
                                <option value="">Seleccione o ano</option>
                                @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(old('academic_year_id')==$year->id)>
                                    {{ $year->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Curso *</label>
                            <select name="course_id" id="course_id" required class="w-full rounded-md border-gray-300">
                                <option value="">Seleccione o curso</option>
                                @foreach($courses as $course)
                                <option value="{{ $course->id }}" @selected(old('course_id')==$course->id)>
                                    {{ $course->code }} — {{ $course->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Turma *</label>
                            <select name="academic_class_id" id="academic_class_id" required class="w-full rounded-md border-gray-300">
                                <option value="">Seleccione a turma</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}" data-year="{{ $class->academic_year_id }}" data-course="{{ $class->course_id }}" data-class-year="{{ $class->year_number }}" data-semester="{{ $class->semester_number }}" @selected(old('academic_class_id')==$class->id)>
                                    {{ $class->name }}
                                    — {{ $class->course->name }}
                                    — {{ $class->academicYear->name }}
                                </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">
                                As turmas serão filtradas pelo ano académico e curso seleccionados.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Ano curricular *</label>
                            <input type="number" name="year_number" id="year_number" min="1" max="20" required value="{{ old('year_number', 1) }}" class="w-full rounded-md border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Semestre</label>
                            <select name="semester_number" id="semester_number" class="w-full rounded-md border-gray-300">
                                <option value="">Não definido</option>
                                @foreach([1, 2, 3] as $semester)
                                <option value="{{ $semester }}" @selected(old('semester_number')==$semester)>
                                    {{ $semester }}.º semestre
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Data da matrícula *</label>
                            <input type="date" name="enrollment_date" required value="{{ old('enrollment_date', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">Tipo de matrícula *</label>
                            <select name="enrollment_type" required class="w-full rounded-md border-gray-300">
                                <option value="regular" @selected(old('enrollment_type', 'regular' )==='regular' )>Regular</option>
                                <option value="transfer" @selected(old('enrollment_type')==='transfer' )>Transferência</option>
                                <option value="reentry" @selected(old('enrollment_type')==='reentry' )>Reingresso</option>
                                <option value="special" @selected(old('enrollment_type')==='special' )>Especial</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-1">Observações</label>
                            <textarea name="observations" rows="3" class="w-full rounded-md border-gray-300">{{ old('observations') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t pt-5">
                    <a href="{{ route('university.enrollments.index') }}" class="px-4 py-2 border rounded-md">
                        Cancelar
                    </a>
                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md">
                        Registar matrícula
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const year = document.getElementById('academic_year_id');
            const course = document.getElementById('course_id');
            const classSelect = document.getElementById('academic_class_id');
            const yearNumber = document.getElementById('year_number');
            const semester = document.getElementById('semester_number');

            function filterClasses() {
                const selectedYear = year.value;
                const selectedCourse = course.value;

                Array.from(classSelect.options).forEach(option => {
                    if (!option.value) return;

                    const visible =
                        (!selectedYear || option.dataset.year === selectedYear) &&
                        (!selectedCourse || option.dataset.course === selectedCourse);

                    option.hidden = !visible;

                    if (!visible && option.selected) {
                        classSelect.value = '';
                    }
                });
            }

            classSelect.addEventListener('change', function() {
                const option = classSelect.selectedOptions[0];

                if (!option || !option.value) return;

                if (option.dataset.classYear) {
                    yearNumber.value = option.dataset.classYear;
                }

                if (option.dataset.semester) {
                    semester.value = option.dataset.semester;
                }
            });

            year.addEventListener('change', filterClasses);
            course.addEventListener('change', filterClasses);

            filterClasses();
        });

    </script>
</x-app-layout>
