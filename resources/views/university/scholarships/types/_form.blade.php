@csrf

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">
    <div>
        <x-input-label for="name" value="Nome da bolsa" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $scholarshipType->name ?? '')" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="code" value="Código" />
        <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $scholarshipType->code ?? '')" required />
        <x-input-error :messages="$errors->get('code')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="type" value="Tipo de bolsa" />
        <select id="type" name="type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
            <option value="percentage" @selected(old('type', $scholarshipType->type ?? 'percentage') === 'percentage')>
                Percentagem (%)
            </option>
            <option value="fixed" @selected(old('type', $scholarshipType->type ?? '') === 'fixed')>
                Valor fixo
            </option>
        </select>
        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="value" value="Valor" />
        <x-text-input id="value" name="value" type="number" step="0.01" min="0.01" class="mt-1 block w-full" :value="old('value', $scholarshipType->value ?? '')" required />
        <x-input-error :messages="$errors->get('value')" class="mt-2" />
        <p class="mt-1 text-xs text-gray-500">
            Para percentagem, introduza um valor entre 1 e 100.
        </p>
    </div>

    <div>
        <x-input-label for="maximum_amount" value="Desconto máximo (opcional)" />
        <x-text-input id="maximum_amount" name="maximum_amount" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('maximum_amount', $scholarshipType->maximum_amount ?? '')" />
        <x-input-error :messages="$errors->get('maximum_amount')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="description" value="Descrição" />
        <textarea id="description" name="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('description', $scholarshipType->description ?? '') }}</textarea>
        <x-input-error :messages="$errors->get('description')" class="mt-2" />
    </div>

    <div class="md:col-span-2">
        <label class="inline-flex items-center">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm" @checked(old('is_active', $scholarshipType->is_active ?? true))>
            <span class="ml-2 text-sm text-gray-700">Bolsa ativa</span>
        </label>
    </div>
</div>

<div class="mt-6 flex justify-end gap-3">
    <a href="{{ route('university.scholarship-types.index') }}" class="rounded-md border px-4 py-2 text-sm text-gray-700">
        Cancelar
    </a>

    <x-primary-button>
        {{ isset($scholarshipType) ? 'Guardar alterações' : 'Criar bolsa' }}
    </x-primary-button>
</div>
