<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Nova contagem física</h2>
                <p class="text-sm text-gray-500 mt-1">Regista as notas e moedas existentes na caixa.</p>
            </div>

            <a href="{{ route('university.treasury.cash-counts.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if($errors->any())
            <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-red-700">
                <ul class="list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if($cashRegisters->isEmpty())
            <div class="bg-white rounded-xl shadow-sm p-6 text-center">
                <p class="text-gray-700 font-medium">Não tens nenhuma caixa aberta atribuída a ti.</p>
                <p class="text-sm text-gray-500 mt-1">Abre uma caixa antes de registar a contagem.</p>
            </div>
            @else
            <form method="POST" action="{{ route('university.treasury.cash-counts.store') }}" class="space-y-5">
                @csrf

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar caixa
                    </label>

                    <select name="cash_register_id" required class="w-full rounded-lg border-gray-300">
                        <option value="">Seleccionar caixa</option>
                        @foreach($cashRegisters as $register)
                        <option value="{{ $register->id }}" @selected(old('cash_register_id')==$register->id)>
                            Caixa #{{ $register->id }} —
                            Saldo esperado:
                            {{ number_format((float) $register->expected_balance, 2, ',', '.') }} AOA
                        </option>
                        @endforeach
                    </select>
                    @error('cash_register_id')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                    <div class="p-5 border-b">
                        <h3 class="font-semibold text-gray-800">Contagem de notas e moedas</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Introduz a quantidade física de cada denominação.
                        </p>
                    </div>

                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($denominations as $value)
                            <div class="border rounded-lg p-4">
                                <div class="flex items-center justify-between mb-2">
                                    <label for="quantity_{{ $value }}" class="font-semibold text-gray-800">
                                        {{ number_format($value, 0, ',', '.') }} AOA
                                    </label>
                                    <span class="text-xs text-gray-400">Quantidade</span>
                                </div>

                                <input id="quantity_{{ $value }}" type="number" min="0" max="100000" step="1" name="quantities[{{ $value }}]" value="{{ old('quantities.'.$value, 0) }}" class="w-full rounded-lg border-gray-300">

                                @error('quantities.'.$value)
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Observações
                    </label>

                    <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300" placeholder="Observações sobre a contagem...">{{ old('notes') }}</textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('university.treasury.cash-counts.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700">
                        Cancelar
                    </a>

                    <button type="submit" onclick="return confirm('Confirma que a contagem física foi concluída correctamente?')" class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                        Submeter contagem
                    </button>
                </div>
            </form>
            @endif
        </div>
    </div>
</x-app-layout>
