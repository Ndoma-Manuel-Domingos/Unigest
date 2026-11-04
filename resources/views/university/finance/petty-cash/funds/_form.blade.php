@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-5">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do fundo</label>
        <input type="text" name="name" value="{{ old('name', $pettyCashFund->name ?? '') }}" required maxlength="150" class="w-full rounded-lg border-gray-300">
        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Código</label>
        <input type="text" name="code" value="{{ old('code', $pettyCashFund->code ?? '') }}" required maxlength="50" class="w-full rounded-lg border-gray-300">
        @error('code') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Responsável</label>
        <select name="responsible_user_id" required class="w-full rounded-lg border-gray-300">
            <option value="">Seleccionar responsável</option>
            @foreach($users as $user)
            <option value="{{ $user->id }}" @selected(old('responsible_user_id', $pettyCashFund->responsible_user_id ?? '') == $user->id)>
                {{ $user->name }}
            </option>
            @endforeach
        </select>
        @error('responsible_user_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
        <select name="department_id" class="w-full rounded-lg border-gray-300">
            <option value="">Sem departamento</option>
            @foreach($departments as $department)
            <option value="{{ $department->id }}" @selected(old('department_id', $pettyCashFund->department_id ?? '') == $department->id)>
                {{ $department->name }}
            </option>
            @endforeach
        </select>
        @error('department_id') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Moeda</label>
        <select name="currency" required class="w-full rounded-lg border-gray-300">
            @foreach(['AOA', 'USD', 'EUR'] as $currency)
            <option value="{{ $currency }}" @selected(old('currency', $pettyCashFund->currency ?? 'AOA') === $currency)>
                {{ $currency }}
            </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Valor autorizado</label>
        <input type="number" name="authorized_amount" min="0.01" step="0.01" value="{{ old('authorized_amount', $pettyCashFund->authorized_amount ?? '') }}" required class="w-full rounded-lg border-gray-300">
        @error('authorized_amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>

    @if(!isset($pettyCashFund))
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Saldo inicial</label>
        <input type="number" name="initial_balance" min="0" step="0.01" value="{{ old('initial_balance', 0) }}" required class="w-full rounded-lg border-gray-300">
        @error('initial_balance') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
    @endif

    <div class="md:col-span-2">
        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
        <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300">{{ old('description', $pettyCashFund->description ?? '') }}</textarea>
        @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>

<div class="flex justify-end gap-3 border-t pt-5 mt-6">
    <a href="{{ route('university.petty-cash.funds.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700">Cancelar</a>

    <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
        {{ isset($pettyCashFund) ? 'Guardar alterações' : 'Criar fundo' }}
    </button>
</div>
