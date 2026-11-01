@csrf

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">

    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Nome da Universidade *
        </label>

        <input type="text" name="name" value="{{ old('name', $university->name ?? '') }}" required class="w-full rounded-md border-gray-300 shadow-sm">

        @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Nome abreviado
        </label>

        <input type="text" name="short_name" value="{{ old('short_name', $university->short_name ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            NIF
        </label>

        <input type="text" name="tax_number" value="{{ old('tax_number', $university->tax_number ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm">

        @error('tax_number')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Email
        </label>

        <input type="email" name="email" value="{{ old('email', $university->email ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm">

        @error('email')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Telefone
        </label>

        <input type="text" name="phone" value="{{ old('phone', $university->phone ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Website
        </label>

        <input type="url" name="website" value="{{ old('website', $university->website ?? '') }}" placeholder="https://exemplo.ao" class="w-full rounded-md border-gray-300 shadow-sm">

        @error('website')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            País *
        </label>

        <input type="text" name="country" value="{{ old('country', $university->country ?? 'Angola') }}" required class="w-full rounded-md border-gray-300 shadow-sm">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Província
        </label>

        <input type="text" name="province" value="{{ old('province', $university->province ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm">
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Município / Cidade
        </label>

        <input type="text" name="city" value="{{ old('city', $university->city ?? '') }}" class="w-full rounded-md border-gray-300 shadow-sm">
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Morada
        </label>

        <textarea name="address" rows="3" class="w-full rounded-md border-gray-300 shadow-sm">{{ old('address', $university->address ?? '') }}</textarea>
    </div>
</div>

<div class="mt-6 flex justify-end gap-3">
    <a href="{{ route('platform.universities.index') }}" class="rounded-md border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
        Cancelar
    </a>

    <button type="submit" class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
        {{ isset($university) ? 'Guardar Alterações' : 'Registar Universidade' }}
    </button>
</div>
