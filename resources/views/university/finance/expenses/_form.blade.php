<div class="grid grid-cols-1 gap-6 md:grid-cols-2">

    <div>
        <label class="block text-sm font-medium text-gray-700">Fornecedor</label>
        <select name="supplier_id" class="mt-1 block w-full rounded-md border-gray-300">
            <option value="">Sem fornecedor associado</option>
            @foreach($suppliers as $supplier)
            <option value="{{ $supplier->id }}" @selected(old('supplier_id', $expense->supplier_id ?? '') == $supplier->id)>
                {{ $supplier->name }} — {{ $supplier->code }}
            </option>
            @endforeach
        </select>
        @error('supplier_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Categoria *</label>
        <select name="category_id" required class="mt-1 block w-full rounded-md border-gray-300">
            <option value="">Seleccione uma categoria</option>
            @foreach($categories as $category)
            <option value="{{ $category->id }}" @selected(old('category_id', $expense->category_id ?? '') == $category->id)>
                {{ $category->name }}
            </option>
            @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Descrição da despesa *</label>
        <input type="text" name="description" required maxlength="255" value="{{ old('description', $expense->description ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300">
        @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Referência / documento</label>
        <input type="text" name="reference" maxlength="100" value="{{ old('reference', $expense->reference ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300">
        @error('reference') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Valor *</label>
        <input type="number" name="amount" required min="0.01" step="0.01" value="{{ old('amount', $expense->amount ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300">
        @error('amount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Moeda *</label>
        <select name="currency" required class="mt-1 block w-full rounded-md border-gray-300">
            @foreach(['AOA' => 'Kwanza (AOA)', 'USD' => 'Dólar (USD)', 'EUR' => 'Euro (EUR)'] as $code => $label)
            <option value="{{ $code }}" @selected(old('currency', $expense->currency ?? 'AOA') === $code)>
                {{ $label }}
            </option>
            @endforeach
        </select>
        @error('currency') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Data da despesa *</label>
        <input type="date" name="expense_date" required value="{{ old('expense_date', isset($expense) ? $expense->expense_date?->format('Y-m-d') : now()->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300">
        @error('expense_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700">Data limite de pagamento</label>
        <input type="date" name="due_date" value="{{ old('due_date', isset($expense) ? $expense->due_date?->format('Y-m-d') : '') }}" class="mt-1 block w-full rounded-md border-gray-300">
        @error('due_date') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Observações</label>
        <textarea name="notes" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('notes', $expense->notes ?? '') }}</textarea>
        @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

</div>

<div class="mt-8 flex justify-end gap-3">
    <a href="{{ route('university.expenses.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">
        Cancelar
    </a>

    <button type="submit" class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
        {{ isset($expense) ? 'Actualizar despesa' : 'Guardar despesa' }}
    </button>
</div>
