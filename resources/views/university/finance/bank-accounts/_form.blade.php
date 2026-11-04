<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Nome da conta *
        </label>

        <input type="text" name="name" value="{{ old('name', $bankAccount->name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

        @error('name')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Banco *
        </label>

        <input type="text" name="bank_name" value="{{ old('bank_name', $bankAccount->bank_name ?? '') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

        @error('bank_name')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Número da conta
        </label>

        <input type="text" name="account_number" value="{{ old('account_number', $bankAccount->account_number ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            IBAN
        </label>

        <input type="text" name="iban" value="{{ old('iban', $bankAccount->iban ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            SWIFT
        </label>

        <input type="text" name="swift" value="{{ old('swift', $bankAccount->swift ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Agência / Balcão
        </label>

        <input type="text" name="branch" value="{{ old('branch', $bankAccount->branch ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Moeda *
        </label>

        <select name="currency" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

            @php
            $currency = old('currency', $bankAccount->currency ?? 'AOA');
            @endphp

            <option value="AOA" {{ $currency === 'AOA' ? 'selected' : '' }}>
                AOA - Kwanza
            </option>

            <option value="USD" {{ $currency === 'USD' ? 'selected' : '' }}>
                USD - Dólar
            </option>

            <option value="EUR" {{ $currency === 'EUR' ? 'selected' : '' }}>
                EUR - Euro
            </option>

        </select>
    </div>

    @if(!isset($bankAccount))

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Saldo inicial *
        </label>

        <input type="number" name="opening_balance" step="0.01" min="0" value="{{ old('opening_balance', 0) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

        @error('opening_balance')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    @endif

    <div class="md:col-span-2">

        <label class="block text-sm font-medium text-gray-700">
            Observações
        </label>

        <textarea name="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('notes', $bankAccount->notes ?? '') }}</textarea>

    </div>

    <div class="md:col-span-2 space-y-3">

        <label class="flex items-center">

            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $bankAccount->is_active ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm">

            <span class="ml-2 text-sm text-gray-700">
                Conta activa
            </span>

        </label>

        <label class="flex items-center">

            <input type="checkbox" name="is_default" value="1" {{ old('is_default', $bankAccount->is_default ?? false) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm">

            <span class="ml-2 text-sm text-gray-700">
                Definir como conta principal
            </span>

        </label>

    </div>

</div>

<div class="mt-6 flex items-center justify-end gap-3">

    <a href="{{ route('university.bank-accounts.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-300">
        Cancelar
    </a>

    <button type="submit" class="px-4 py-2 bg-indigo-600 rounded-md text-sm font-semibold text-white hover:bg-indigo-700">
        {{ $submitLabel ?? 'Guardar' }}
    </button>

</div>
