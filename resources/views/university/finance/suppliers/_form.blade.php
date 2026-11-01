<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div>
        <label class="block text-sm font-medium text-gray-700">Nome do fornecedor *</label>
        <input type="text" name="name" value="{{ old('name', $supplier->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Código *</label>
        <input type="text" name="code" value="{{ old('code', $supplier->code ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('code') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">NIF</label>
        <input type="text" name="tax_number" value="{{ old('tax_number', $supplier->tax_number ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('tax_number') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">E-mail</label>
        <input type="email" name="email" value="{{ old('email', $supplier->email ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Telefone</label>
        <input type="text" name="phone" value="{{ old('phone', $supplier->phone ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('phone') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Cidade</label>
        <input type="text" name="city" value="{{ old('city', $supplier->city ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('city') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Morada</label>
        <input type="text" name="address" value="{{ old('address', $supplier->address ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('address') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Banco</label>
        <input type="text" name="bank_name" value="{{ old('bank_name', $supplier->bank_name ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('bank_name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Conta bancária</label>
        <input type="text" name="bank_account" value="{{ old('bank_account', $supplier->bank_account ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('bank_account') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">IBAN</label>
        <input type="text" name="iban" value="{{ old('iban', $supplier->iban ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        @error('iban') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Observações</label>
        <textarea name="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes', $supplier->notes ?? '') }}</textarea>
        @error('notes') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    @if(!isset($supplier))
    <div class="md:col-span-2">
        <input type="hidden" name="is_active" value="0">

        <label class="inline-flex items-center">
            <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-indigo-600 shadow-sm">
            <span class="ml-2 text-sm text-gray-700">Fornecedor activo</span>
        </label>
    </div>
    @endif

</div>

<div class="mt-8 flex justify-end gap-3">
    <a href="{{ route('university.suppliers.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
        Cancelar
    </a>

    <button type="submit" class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
        {{ isset($supplier) ? 'Actualizar fornecedor' : 'Guardar fornecedor' }}
    </button>
</div>
