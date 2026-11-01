@csrf

@if(isset($academicYear))
@method('PUT')
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">

    <div>
        <label class="block font-medium mb-1">Designação do Ano Lectivo *</label>
        <input type="text" name="name" value="{{ old('name', $academicYear->name ?? '') }}" placeholder="Ex.: 2026/2027" required class="w-full rounded-lg border-gray-300">

        @error('name')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Data de Início *</label>
        <input type="date" name="start_date" value="{{ old('start_date', isset($academicYear) ? $academicYear->start_date->format('Y-m-d') : '') }}" required class="w-full rounded-lg border-gray-300">

        @error('start_date')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Data de Fim *</label>
        <input type="date" name="end_date" value="{{ old('end_date', isset($academicYear) ? $academicYear->end_date->format('Y-m-d') : '') }}" required class="w-full rounded-lg border-gray-300">

        @error('end_date')
        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block font-medium mb-1">Descrição</label>
        <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300">{{ old('description', $academicYear->description ?? '') }}</textarea>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $academicYear->is_active ?? true))
        class="rounded border-gray-300">

        <label> Ano lectivo activo</label>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_current" value="1" @checked(old('is_current', $academicYear->is_current ?? false))
        class="rounded border-gray-300">

        <label> Definir como ano lectivo actual</label>
    </div>
</div>

<div class="flex justify-end gap-3 mt-6">
    <a href="{{ route('university.academic-years.index') }}" class="bg-gray-200 px-5 py-2 rounded-lg">
        Cancelar
    </a>

    <button type="submit" class="bg-indigo-600 text-white px-5 py-2 rounded-lg hover:bg-indigo-700">
        Guardar
    </button>
</div>
