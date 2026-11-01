@csrf

<div class="space-y-5">

    <div>
        <label class="block text-sm font-medium mb-1">Faculdade *</label>

        <select name="faculty_id" required class="w-full border-gray-300 rounded-md">
            <option value="">Seleccione a faculdade</option>

            @foreach($faculties as $faculty)
            <option value="{{ $faculty->id }}" @selected((string) old('faculty_id', $department->faculty_id ?? '') === (string) $faculty->id)>
                {{ $faculty->name }}
            </option>
            @endforeach
        </select>

        @error('faculty_id')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Nome do Departamento *</label>

        <input type="text" name="name" value="{{ old('name', $department->name ?? '') }}" maxlength="150" required class="w-full border-gray-300 rounded-md">

        @error('name')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Sigla</label>

        <input type="text" name="acronym" value="{{ old('acronym', $department->acronym ?? '') }}" maxlength="30" class="w-full border-gray-300 rounded-md">

        @error('acronym')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium mb-1">Descrição</label>

        <textarea name="description" rows="4" class="w-full border-gray-300 rounded-md">{{ old('description', $department->description ?? '') }}</textarea>

        @error('description')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex gap-3">
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded">
            Guardar
        </button>

        <a href="{{ route('university.departments.index') }}" class="px-5 py-2 bg-gray-200 rounded">
            Cancelar
        </a>
    </div>

</div>
