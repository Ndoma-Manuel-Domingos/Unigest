<div class="space-y-6">

    <div>
        <label for="name" class="mb-2 block text-sm font-semibold text-gray-700">
            Nome
        </label>

        <input type="text" name="name" id="name" value="{{ old('name', $feeConcept->name ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ex.: Matrícula" required>

        @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="code" class="mb-2 block text-sm font-semibold text-gray-700">
            Código
        </label>

        <input type="text" name="code" id="code" value="{{ old('code', $feeConcept->code ?? '') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ex.: MAT" required>

        @error('code')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">
            Descrição
        </label>

        <textarea name="description" id="description" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Descrição do conceito de taxa">{{ old('description', $feeConcept->description ?? '') }}</textarea>

        @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-lg border border-gray-200 p-4">
        <label class="flex items-center gap-3">
            <input type="checkbox" name="is_recurring" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_recurring', $feeConcept->is_recurring ?? false))>

            <span>
                <span class="block text-sm font-semibold text-gray-800">
                    Taxa recorrente
                </span>
                <span class="block text-xs text-gray-500">
                    Indica se este conceito pode ser cobrado periodicamente.
                </span>
            </span>
        </label>

        @error('is_recurring')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="rounded-lg border border-gray-200 p-4">
        <label class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_active', $feeConcept->is_active ?? true))>

            <span>
                <span class="block text-sm font-semibold text-gray-800">
                    Conceito activo
                </span>
                <span class="block text-xs text-gray-500">
                    Apenas conceitos activos ficam disponíveis para novas cobranças.
                </span>
            </span>
        </label>

        @error('is_active')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

</div>
