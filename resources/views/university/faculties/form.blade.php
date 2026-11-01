@csrf

<div class="space-y-5">

    <div>
        <label class="block font-medium text-sm mb-1">
            Nome da Faculdade *
        </label>

        <input type="text" name="name" value="{{ old('name', $faculty->name ?? '') }}" required maxlength="150" class="w-full border-gray-300 rounded-md">

        @error('name')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block font-medium text-sm mb-1">
            Sigla
        </label>

        <input type="text" name="acronym" value="{{ old('acronym', $faculty->acronym ?? '') }}" maxlength="30" class="w-full border-gray-300 rounded-md">

        @error('acronym')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div>
        <label class="block font-medium text-sm mb-1">
            Descrição
        </label>

        <textarea name="description" rows="4" class="w-full border-gray-300 rounded-md">{{ old('description', $faculty->description ?? '') }}</textarea>

        @error('description')
        <span class="text-red-600 text-sm">{{ $message }}</span>
        @enderror
    </div>

    <div class="flex gap-3">
        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-md">
            Guardar
        </button>

        <a href="{{ route('university.faculties.index') }}" class="px-5 py-2 bg-gray-200 rounded-md">
            Cancelar
        </a>
    </div>

</div>
