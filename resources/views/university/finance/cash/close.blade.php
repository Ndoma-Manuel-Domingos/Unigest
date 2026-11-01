<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Fechar Caixa</h2>
                <p class="text-sm text-gray-500 mt-1">Caixa #{{ $cashRegister->id }}</p>
            </div>

            <a href="{{ route('university.cash.show', $cashRegister) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Voltar ao Caixa</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-xl p-6 space-y-6">

                @if($errors->any())
                <div class="p-4 bg-red-100 text-red-700 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500">Saldo inicial</p>
                        <p class="text-lg font-bold">
                            {{ number_format($cashRegister->opening_balance, 2, ',', '.') }} AOA
                        </p>
                    </div>

                    <div class="p-4 bg-green-50 rounded-lg">
                        <p class="text-sm text-gray-500">Total de entradas</p>
                        <p class="text-lg font-bold text-green-700">
                            {{ number_format($income, 2, ',', '.') }} AOA
                        </p>
                    </div>

                    <div class="p-4 bg-red-50 rounded-lg">
                        <p class="text-sm text-gray-500">Total de saídas</p>
                        <p class="text-lg font-bold text-red-700">
                            {{ number_format($expense, 2, ',', '.') }} AOA
                        </p>
                    </div>

                    <div class="p-4 bg-indigo-50 rounded-lg">
                        <p class="text-sm text-gray-500">Saldo esperado</p>
                        <p class="text-lg font-bold text-indigo-700">
                            {{ number_format($expectedBalance, 2, ',', '.') }} AOA
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('university.cash.close.store', $cashRegister) }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Saldo contado fisicamente (AOA) *
                        </label>
                        <input type="number" name="counted_balance" min="0" step="0.01" value="{{ old('counted_balance', number_format($expectedBalance, 2, '.', '')) }}" required class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Observações do fecho
                        </label>
                        <textarea name="closing_notes" rows="3" class="w-full rounded-lg border-gray-300">{{ old('closing_notes') }}</textarea>
                    </div>

                    <div class="p-4 bg-yellow-50 text-yellow-800 rounded-lg text-sm">
                        Depois de fechar, este caixa não poderá receber novos movimentos.
                        Confirme que o dinheiro foi contado antes de continuar.
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('university.cash.show', $cashRegister) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Cancelar</a>

                        <button type="submit" onclick="return confirm('Tem a certeza de que pretende fechar este caixa?')" class="px-5 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Confirmar Fecho
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
