<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Registar Movimento</h2>
                <p class="text-sm text-gray-500 mt-1">Caixa #{{ $cashRegister->id }}</p>
            </div>

            <a href="{{ route('university.cash.show', $cashRegister) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Voltar ao Caixa</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl p-6">
                @if($errors->any())
                <div class="mb-5 p-4 bg-red-100 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('university.cash.movement.store', $cashRegister) }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de movimento *</label>
                        <select name="type" required class="w-full rounded-lg border-gray-300">
                            <option value="income" @selected(old('type')==='income' )>Entrada de dinheiro</option>
                            <option value="expense" @selected(old('type')==='expense' )>Saída de dinheiro</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição *</label>
                        <input type="text" name="description" value="{{ old('description') }}" required maxlength="255" class="w-full rounded-lg border-gray-300" placeholder="Ex.: Compra de material de escritório">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor (AOA) *</label>
                        <input type="number" name="amount" value="{{ old('amount') }}" min="0.01" step="0.01" required class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Data do movimento *</label>
                        <input type="datetime-local" name="movement_date" value="{{ old('movement_date', now()->format('Y-m-d\TH:i')) }}" required class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                        <textarea name="observations" rows="3" class="w-full rounded-lg border-gray-300">{{ old('observations') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('university.cash.show', $cashRegister) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancelar</a>

                        <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Guardar Movimento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
