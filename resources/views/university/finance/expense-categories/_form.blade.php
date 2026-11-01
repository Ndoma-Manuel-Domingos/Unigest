<div class="grid grid-cols-1 gap-6 md:grid-cols-2">

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Nome da categoria *
        </label>

        <input type="text" name="name" value="{{ old('name', $expenseCategory->name ?? '') }}" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

        @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Código *
        </label>

        <input type="text" name="code" value="{{ old('code', $expenseCategory->code ?? '') }}" required maxlength="50" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

        @error('code')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">
            Descrição
        </label>

        <textarea name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $expenseCategory->description ?? '') }}</textarea>

        @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    @if(!isset($expenseCategory))
    <div class="md:col-span-2">
        <input type="hidden" name="is_active" value="0">

        <label class="inline-flex items-center">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm">

            <span class="ml-2 text-sm text-gray-700">
                Categoria activa
            </span>
        </label>
    </div>
    @endif

</div>

<div class="mt-8 flex justify-end gap-3">
    <a href="{{ route('university.expense-categories.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
        Cancelar
    </a>

    <button type="submit" class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
        {{ isset($expenseCategory) ? 'Actualizar categoria' : 'Guardar categoria' }}
    </button>
</div>
