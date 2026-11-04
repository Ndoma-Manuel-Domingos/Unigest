<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Nova transferência entre caixas</h2>
                <p class="text-sm text-gray-500 mt-1">Enviar valores para outro operador.</p>
            </div>

            <a href="{{ route('university.treasury.transfers.index') }}" class="px-4 py-2 bg-gray-100 rounded-lg text-sm text-gray-700 hover:bg-gray-200">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="mb-6 rounded-lg bg-blue-50 border border-blue-100 p-4 text-sm text-blue-800">
                    A transferência ficará pendente. O saldo só será alterado depois da confirmação do operador de destino.
                </div>

                @if($errors->any())
                <div class="mb-5 rounded-lg bg-red-50 border border-red-200 p-4 text-red-700">
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('university.treasury.transfers.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Caixa de origem</label>
                        <select name="source_cash_register_id" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccionar caixa de origem</option>
                            @foreach($cashRegisters as $register)
                            <option value="{{ $register->id }}" @selected(old('source_cash_register_id')==$register->id)>
                                Caixa #{{ $register->id }} —
                                {{ $register->user?->name ?? 'Operador' }} —
                                Saldo: {{ number_format((float) $register->expected_balance, 2, ',', '.') }} AOA
                            </option>
                            @endforeach
                        </select>
                        @error('source_cash_register_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Caixa de destino</label>
                        <select name="destination_cash_register_id" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccionar caixa de destino</option>
                            @foreach($cashRegisters as $register)
                            <option value="{{ $register->id }}" @selected(old('destination_cash_register_id')==$register->id)>
                                Caixa #{{ $register->id }} —
                                {{ $register->user?->name ?? 'Operador' }}
                            </option>
                            @endforeach
                        </select>
                        @error('destination_cash_register_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Valor a transferir (AOA)</label>
                        <input type="number" name="amount" min="0.01" step="0.01" value="{{ old('amount') }}" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('amount')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Motivo ou informação adicional">{{ old('notes') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <a href="{{ route('university.treasury.transfers.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700">
                            Cancelar
                        </a>

                        <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                            Criar transferência
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
