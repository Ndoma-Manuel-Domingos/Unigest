@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div>
        <label class="block text-sm font-medium mb-1">
            Faculdade *
        </label>

        <select name="faculty_id" required class="w-full border-gray-300 rounded-md">
            <option value="">Seleccione</option>

            @foreach($faculties as $faculty)
            <option value="{{ $faculty->id }}" @selected((string) old('faculty_id', $course->faculty_id ?? '') === (string) $faculty->id)>
                {{ $faculty->name }}
            </option>
            @endforeach
        </select>

        @error('faculty_id')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Departamento
        </label>

        <select name="department_id" class="w-full border-gray-300 rounded-md">
            <option value="">Nenhum</option>

            @foreach($departments as $department)
            <option value="{{ $department->id }}" @selected((string) old('department_id', $course->department_id ?? '') === (string) $department->id)>
                {{ $department->name }} — {{ $department->faculty->name }}
            </option>
            @endforeach
        </select>

        @error('department_id')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Nome do Curso *
        </label>

        <input type="text" name="name" value="{{ old('name', $course->name ?? '') }}" required maxlength="200" class="w-full border-gray-300 rounded-md">

        @error('name')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Código *
        </label>

        <input type="text" name="code" value="{{ old('code', $course->code ?? '') }}" required maxlength="50" class="w-full border-gray-300 rounded-md">

        @error('code')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Sigla
        </label>

        <input type="text" name="acronym" value="{{ old('acronym', $course->acronym ?? '') }}" maxlength="30" class="w-full border-gray-300 rounded-md">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Grau académico *
        </label>

        <select name="degree_type" required class="w-full border-gray-300 rounded-md">

            @php
            $degrees = [
            'licenciatura' => 'Licenciatura',
            'bacharelato' => 'Bacharelato',
            'mestrado' => 'Mestrado',
            'doutoramento' => 'Doutoramento',
            'pos_graduacao' => 'Pós-graduação',
            'curso_tecnico' => 'Curso Técnico',
            'outro' => 'Outro',
            ];
            @endphp

            @foreach($degrees as $value => $label)
            <option value="{{ $value }}" @selected(old('degree_type', $course->degree_type ?? '') === $value)>
                {{ $label }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Duração em anos
        </label>

        <input type="number" name="duration_years" min="1" max="10" value="{{ old('duration_years', $course->duration_years ?? '') }}" class="w-full border-gray-300 rounded-md">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Duração em semestres
        </label>

        <input type="number" name="duration_semesters" min="1" max="20" value="{{ old('duration_semesters', $course->duration_semesters ?? '') }}" class="w-full border-gray-300 rounded-md">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Regime *
        </label>

        <select name="study_regime" required class="w-full border-gray-300 rounded-md">

            @foreach([
            'presencial' => 'Presencial',
            'pos_laboral' => 'Pós-laboral',
            'online' => 'Online',
            'hibrido' => 'Híbrido',
            ] as $value => $label)

            <option value="{{ $value }}" @selected(old('study_regime', $course->study_regime ?? 'presencial') === $value)>
                {{ $label }}
            </option>

            @endforeach
        </select>
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium mb-1">
            Descrição
        </label>

        <textarea name="description" rows="4" class="w-full border-gray-300 rounded-md">{{ old('description', $course->description ?? '') }}</textarea>
    </div>

    <div class="md:col-span-2 border-t pt-5">

        <label class="flex items-center gap-2">
            <input type="checkbox" name="is_accredited" value="1" @checked(old('is_accredited', $course->is_accredited ?? false))>

            <span class="font-medium">
                Curso acreditado
            </span>
        </label>

    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Número de acreditação
        </label>

        <input type="text" name="accreditation_number" value="{{ old('accreditation_number', $course->accreditation_number ?? '') }}" maxlength="100" class="w-full border-gray-300 rounded-md">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Data de acreditação
        </label>

        <input type="date" name="accreditation_date" value="{{ old('accreditation_date', isset($course) && $course->accreditation_date ? $course->accreditation_date->format('Y-m-d') : '') }}" class="w-full border-gray-300 rounded-md">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Validade da acreditação
        </label>

        <input type="date" name="accreditation_expiry" value="{{ old('accreditation_expiry', isset($course) && $course->accreditation_expiry ? $course->accreditation_expiry->format('Y-m-d') : '') }}" class="w-full border-gray-300 rounded-md">
    </div>

</div>

<div class="flex gap-3 mt-6">

    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md">
        Guardar
    </button>

    <a href="{{ route('university.courses.index') }}" class="px-5 py-2 bg-gray-200 rounded-md">
        Cancelar
    </a>

</div>
