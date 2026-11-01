@csrf

@if(isset($academicClass))
@method('PUT')
@endif

@php
$selectedCourse = old('course_id', $academicClass->course_id ?? '');
$selectedCurriculum = old('curriculum_id', $academicClass->curriculum_id ?? '');
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div>
        <label class="block font-medium mb-1">Ano Lectivo *</label>
        <select name="academic_year_id" required class="w-full rounded-lg border-gray-300">
            <option value="">Seleccione o ano lectivo</option>
            @foreach($academicYears as $year)
            <option value="{{ $year->id }}" @selected(old('academic_year_id', $academicClass->academic_year_id ?? '') == $year->id)>
                {{ $year->name }}
            </option>
            @endforeach
        </select>
        @error('academic_year_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Curso *</label>
        <select name="course_id" id="course_id" required class="w-full rounded-lg border-gray-300">
            <option value="">Seleccione o curso</option>
            @foreach($courses as $course)
            <option value="{{ $course->id }}" @selected($selectedCourse==$course->id)>
                {{ $course->name }} ({{ $course->code }})
            </option>
            @endforeach
        </select>
        @error('course_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Plano Curricular</label>
        <select name="curriculum_id" id="curriculum_id" class="w-full rounded-lg border-gray-300">
            <option value="">Sem plano curricular associado</option>
            @foreach($curricula as $curriculum)
            <option value="{{ $curriculum->id }}" data-course="{{ $curriculum->course_id }}" @selected($selectedCurriculum==$curriculum->id)>
                {{ $curriculum->name }} — {{ $curriculum->course->name }}
            </option>
            @endforeach
        </select>
        @error('curriculum_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Nome da Turma *</label>
        <input type="text" name="name" value="{{ old('name', $academicClass->name ?? '') }}" placeholder="Ex.: Turma A" required class="w-full rounded-lg border-gray-300">
        @error('name') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Código da Turma *</label>
        <input type="text" name="code" value="{{ old('code', $academicClass->code ?? '') }}" placeholder="Ex.: INF-2026-A" required class="w-full rounded-lg border-gray-300">
        @error('code') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Ano Curricular *</label>
        <select name="year_number" required class="w-full rounded-lg border-gray-300">
            <option value="">Seleccione</option>
            @for($i = 1; $i <= 10; $i++) <option value="{{ $i }}" @selected(old('year_number', $academicClass->year_number ?? '') == $i)>
                {{ $i }}º Ano
                </option>
                @endfor
        </select>
        @error('year_number') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Semestre *</label>
        <select name="semester_number" required class="w-full rounded-lg border-gray-300">
            <option value="">Seleccione</option>
            @foreach([1, 2, 3] as $semester)
            <option value="{{ $semester }}" @selected(old('semester_number', $academicClass->semester_number ?? '') == $semester)>
                {{ $semester }}º Semestre
            </option>
            @endforeach
        </select>
        @error('semester_number') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Turno *</label>
        <select name="shift" required class="w-full rounded-lg border-gray-300">
            <option value="">Seleccione</option>
            @foreach([
            'manha' => 'Manhã',
            'tarde' => 'Tarde',
            'noite' => 'Noite',
            'integral' => 'Integral',
            'pos_laboral' => 'Pós-laboral',
            'outro' => 'Outro'
            ] as $value => $label)
            <option value="{{ $value }}" @selected(old('shift', $academicClass->shift ?? '') === $value)>
                {{ $label }}
            </option>
            @endforeach
        </select>
        @error('shift') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Regime de Ensino *</label>
        <select name="study_regime" required class="w-full rounded-lg border-gray-300">
            <option value="">Seleccione</option>
            <option value="presencial" @selected(old('study_regime', $academicClass->study_regime ?? '') === 'presencial')>Presencial</option>
            <option value="online" @selected(old('study_regime', $academicClass->study_regime ?? '') === 'online')>Online</option>
            <option value="hibrido" @selected(old('study_regime', $academicClass->study_regime ?? '') === 'hibrido')>Híbrido</option>
        </select>
        @error('study_regime') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Capacidade máxima *</label>
        <input type="number" name="capacity" min="1" max="10000" value="{{ old('capacity', $academicClass->capacity ?? 40) }}" required class="w-full rounded-lg border-gray-300">
        @error('capacity') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Data de Início</label>
        <input type="date" name="start_date" value="{{ old('start_date', isset($academicClass) && $academicClass->start_date ? $academicClass->start_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300">
        @error('start_date') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Data de Fim</label>
        <input type="date" name="end_date" value="{{ old('end_date', isset($academicClass) && $academicClass->end_date ? $academicClass->end_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300">
        @error('end_date') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block font-medium mb-1">Descrição</label>
        <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300">{{ old('description', $academicClass->description ?? '') }}</textarea>
        @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center gap-2 md:col-span-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $academicClass->is_active ?? true))
        class="rounded border-gray-300">
        <label>Turma activa</label>
    </div>
</div>

<div class="flex justify-end gap-3 mt-6">
    <a href="{{ route('university.classes.index') }}" class="bg-gray-200 px-5 py-2 rounded-lg">
        Cancelar
    </a>

    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">
        Guardar Turma
    </button>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const course = document.getElementById('course_id');
        const curriculum = document.getElementById('curriculum_id');

        function filterCurricula() {
            const courseId = course.value;

            Array.from(curriculum.options).forEach(option => {
                if (!option.value) return;

                const matches = option.dataset.course === courseId;
                option.hidden = !matches;

                if (!matches && option.selected) {
                    curriculum.value = '';
                }
            });
        }

        course.addEventListener('change', filterCurricula);
        filterCurricula();
    });

</script>
