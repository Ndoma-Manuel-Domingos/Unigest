@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div>
        <label class="block text-sm font-medium mb-1">
            Departamento
        </label>

        <select name="department_id" class="w-full border-gray-300 rounded-md">

            <option value="">Sem departamento</option>

            @foreach($departments as $department)
            <option value="{{ $department->id }}" @selected( (string) old( 'department_id' , $subject->department_id ?? ''
                ) === (string) $department->id
                )>
                {{ $department->name }}
            </option>
            @endforeach

        </select>

        @error('department_id')
        <span class="text-red-600 text-sm">
            {{ $message }}
        </span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Nome da Disciplina *
        </label>

        <input type="text" name="name" required maxlength="200" value="{{ old('name', $subject->name ?? '') }}" class="w-full border-gray-300 rounded-md">

        @error('name')
        <span class="text-red-600 text-sm">
            {{ $message }}
        </span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Código *
        </label>

        <input type="text" name="code" required maxlength="50" value="{{ old('code', $subject->code ?? '') }}" class="w-full border-gray-300 rounded-md">

        @error('code')
        <span class="text-red-600 text-sm">
            {{ $message }}
        </span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Tipo *
        </label>

        <select name="type" required class="w-full border-gray-300 rounded-md">

            @foreach([
            'obrigatoria' => 'Obrigatória',
            'opcional' => 'Opcional',
            'electiva' => 'Electiva',
            'livre' => 'Livre',
            ] as $value => $label)

            <option value="{{ $value }}" @selected( old( 'type' , $subject->type ?? 'obrigatoria'
                ) === $value
                )>
                {{ $label }}
            </option>

            @endforeach

        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Ano curricular
        </label>

        <select name="year_number" class="w-full border-gray-300 rounded-md">

            <option value="">Não definido</option>

            @for($year = 1; $year <= 10; $year++) <option value="{{ $year }}" @selected( (string) old( 'year_number' , $subject->year_number ?? ''
                ) === (string) $year
                )>
                {{ $year }}º Ano
                </option>
                @endfor

        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Semestre
        </label>

        <select name="semester_number" class="w-full border-gray-300 rounded-md">

            <option value="">Não definido</option>

            @for($semester = 1; $semester <= 4; $semester++) <option value="{{ $semester }}" @selected( (string) old( 'semester_number' , $subject->semester_number ?? ''
                ) === (string) $semester
                )>
                {{ $semester }}º Semestre
                </option>
                @endfor

        </select>
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Créditos *
        </label>

        <input type="number" name="credits" min="0" max="100" required value="{{ old('credits', $subject->credits ?? 0) }}" class="w-full border-gray-300 rounded-md">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Horas teóricas *
        </label>

        <input type="number" name="theoretical_hours" min="0" max="2000" required value="{{ old('theoretical_hours', $subject->theoretical_hours ?? 0) }}" class="w-full border-gray-300 rounded-md">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Horas práticas *
        </label>

        <input type="number" name="practical_hours" min="0" max="2000" required value="{{ old('practical_hours', $subject->practical_hours ?? 0) }}" class="w-full border-gray-300 rounded-md">
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">
            Horas de laboratório *
        </label>

        <input type="number" name="laboratory_hours" min="0" max="2000" required value="{{ old('laboratory_hours', $subject->laboratory_hours ?? 0) }}" class="w-full border-gray-300 rounded-md">
    </div>

    <div class="md:col-span-2">

        <label class="flex items-center gap-2">

            <input type="checkbox" name="requires_prerequisite" value="1" @checked( old( 'requires_prerequisite' , $subject->requires_prerequisite ?? false
            )
            )>

            <span>
                Esta disciplina exige pré-requisito
            </span>

        </label>

    </div>

    <div class="md:col-span-2">

        <label class="block text-sm font-medium mb-1">
            Descrição
        </label>

        <textarea name="description" rows="4" class="w-full border-gray-300 rounded-md">{{ old('description', $subject->description ?? '') }}</textarea>

    </div>

</div>

<div class="flex gap-3 mt-6">

    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md">
        Guardar
    </button>

    <a href="{{ route('university.subjects.index') }}" class="px-5 py-2 bg-gray-200 rounded-md">
        Cancelar
    </a>

</div>
