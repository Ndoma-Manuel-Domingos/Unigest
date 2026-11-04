<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Conta bancária *
        </label>

        <select name="bank_account_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

            <option value="">Seleccione a conta</option>

            @foreach($accounts as $account)
            <option value="{{ $account->id }}" {{ old('bank_account_id') == $account->id ? 'selected' : '' }}>

                {{ $account->name }}
                — {{ $account->bank_name }}
                — {{ number_format($account->current_balance, 2, ',', '.') }}
                {{ $account->currency }}

            </option>
            @endforeach

        </select>

        @error('bank_account_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Tipo *
        </label>
        <select name="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

            <option value="">Seleccione</option>

            <option value="income" {{ old('type') === 'income' ? 'selected' : '' }}>
                Entrada
            </option>

            <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>
                Saída
            </option>

            <option value="adjustment" {{ old('type') === 'adjustment' ? 'selected' : '' }}>
                Ajuste
            </option>

        </select>

        @error('type')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div id="adjustment-direction-field" class="hidden">
        <label class="block text-sm font-medium text-gray-700">
            Sentido do ajuste *
        </label>

        <select name="adjustment_direction" id="adjustment_direction" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
            <option value="">Seleccione</option>
            <option value="positive" {{ old('adjustment_direction') === 'positive' ? 'selected' : '' }}>
                Ajuste positivo — aumentar saldo
            </option>
            <option value="negative" {{ old('adjustment_direction') === 'negative' ? 'selected' : '' }}>
                Ajuste negativo — reduzir saldo
            </option>
        </select>

        @error('adjustment_direction')
        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Valor *
        </label>

        <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

        @error('amount')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Data *
        </label>

        <input type="date" name="movement_date" value="{{ old('movement_date', now()->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

        @error('movement_date')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">
            Descrição *
        </label>

        <input type="text" name="description" value="{{ old('description') }}" required maxlength="255" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">

        @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Referência
        </label>

        <input type="text" name="reference" value="{{ old('reference') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Nº do documento
        </label>

        <input type="text" name="document_number" value="{{ old('document_number') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">
            Referência da transacção
        </label>

        <input type="text" name="transaction_reference" value="{{ old('transaction_reference') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">
            Observações
        </label>

        <textarea name="observations" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">{{ old('observations') }}</textarea>
    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const type = document.querySelector('[name="type"]');
        const field = document.getElementById('adjustment-direction-field');
        const direction = document.getElementById('adjustment_direction');

        function toggleAdjustment() {
            const isAdjustment = type.value === 'adjustment';

            field.classList.toggle('hidden', !isAdjustment);
            direction.required = isAdjustment;

            if (!isAdjustment) {
                direction.value = '';
            }
        }

        type.addEventListener('change', toggleAdjustment);
        toggleAdjustment();
    });

</script>


<div class="mt-6 flex justify-end gap-3">

    <a href="{{ route('university.bank-movements.index') }}" class="px-4 py-2 bg-gray-200 rounded-md text-sm text-gray-700">
        Cancelar
    </a>

    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-semibold hover:bg-indigo-700">
        Registar Movimento
    </button>

</div>
