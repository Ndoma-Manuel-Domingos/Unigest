<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div>
        <label for="bank_account_id" class="block text-sm font-medium text-gray-700">
            Conta bancária
        </label>

        <select name="bank_account_id" id="bank_account_id" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

            <option value="">Selecione a conta</option>

            @foreach($bankAccounts as $account)
            <option value="{{ $account->id }}" @selected(old('bank_account_id', $bankReconciliation->bank_account_id ?? '') == $account->id)>
                {{ $account->name }}
                — {{ $account->bank_name }}
                — {{ $account->currency }}
            </option>
            @endforeach
        </select>

        @error('bank_account_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="reference" class="block text-sm font-medium text-gray-700">
            Referência
        </label>

        <input type="text" id="reference" value="{{ $bankReconciliation->reference ?? 'Gerada automaticamente' }}" disabled class="mt-1 block w-full rounded-lg border-gray-300 bg-gray-100 shadow-sm">
    </div>

    <div>
        <label for="period_start" class="block text-sm font-medium text-gray-700">
            Data inicial
        </label>

        <input type="date" name="period_start" id="period_start" value="{{ old('period_start', isset($bankReconciliation) ? $bankReconciliation->period_start?->format('Y-m-d') : '') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

        @error('period_start')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="period_end" class="block text-sm font-medium text-gray-700">
            Data final
        </label>

        <input type="date" name="period_end" id="period_end" value="{{ old('period_end', isset($bankReconciliation) ? $bankReconciliation->period_end?->format('Y-m-d') : '') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

        @error('period_end')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="statement_opening_balance" class="block text-sm font-medium text-gray-700">
            Saldo inicial do extrato
        </label>

        <input type="number" name="statement_opening_balance" id="statement_opening_balance" step="0.01" min="0" value="{{ old('statement_opening_balance', $bankReconciliation->statement_opening_balance ?? '') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

        @error('statement_opening_balance')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="statement_closing_balance" class="block text-sm font-medium text-gray-700">
            Saldo final do extrato
        </label>

        <input type="number" name="statement_closing_balance" id="statement_closing_balance" step="0.01" min="0" value="{{ old('statement_closing_balance', $bankReconciliation->statement_closing_balance ?? '') }}" required class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

        @error('statement_closing_balance')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

</div>

<div class="mt-6">
    <label for="notes" class="block text-sm font-medium text-gray-700">
        Observações
    </label>

    <textarea name="notes" id="notes" rows="4" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $bankReconciliation->notes ?? '') }}</textarea>

    @error('notes')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mt-6 flex justify-end gap-3">
    <a href="{{ route('university.bank-reconciliations.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
        Cancelar
    </a>

    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
        Criar Reconciliação
    </button>
</div>
