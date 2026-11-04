<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">
                    Transferência entre Contas
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Transferir valores entre contas bancárias da universidade.
                </p>
            </div>

            <a href="{{ route('university.bank-movements.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
            <div class="mb-5 bg-red-100 border border-red-300 text-red-800 p-4 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">

                <div class="mb-6 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 text-sm">
                    A transferência gera automaticamente uma saída na conta de origem e uma entrada na conta de destino.
                </div>

                <form method="POST" action="{{ route('university.bank-movements.transfer.store') }}">
                    @csrf

                    <div class="space-y-5">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Conta de origem *
                            </label>

                            <select name="source_account_id" required class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Seleccione a conta de origem</option>

                                @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('source_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} — {{ $account->bank_name }}
                                    ({{ number_format($account->current_balance, 2, ',', '.') }} {{ $account->currency }})
                                </option>
                                @endforeach
                            </select>

                            @error('source_account_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Conta de destino *
                            </label>

                            <select name="destination_account_id" required class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Seleccione a conta de destino</option>

                                @foreach($accounts as $account)
                                <option value="{{ $account->id }}" {{ old('destination_account_id') == $account->id ? 'selected' : '' }}>
                                    {{ $account->name }} — {{ $account->bank_name }}
                                    ({{ $account->currency }})
                                </option>
                                @endforeach
                            </select>

                            @error('destination_account_id')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Valor *
                            </label>

                            <input type="number" name="amount" min="0.01" step="0.01" value="{{ old('amount') }}" required class="mt-1 block w-full rounded-md border-gray-300">

                            @error('amount')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Data da transferência *
                            </label>

                            <input type="date" name="movement_date" value="{{ old('movement_date', now()->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300">

                            @error('movement_date')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Descrição *
                            </label>

                            <input type="text" name="description" value="{{ old('description') }}" maxlength="255" required class="mt-1 block w-full rounded-md border-gray-300">

                            @error('description')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Referência da transacção bancária
                            </label>

                            <input type="text" name="transaction_reference" value="{{ old('transaction_reference') }}" class="mt-1 block w-full rounded-md border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">
                                Observações
                            </label>

                            <textarea name="observations" rows="3" class="mt-1 block w-full rounded-md border-gray-300">{{ old('observations') }}</textarea>
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('university.bank-movements.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md">
                            Cancelar
                        </a>

                        <button type="submit" onclick="return confirm('Confirma esta transferência bancária?')" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Confirmar Transferência
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</x-app-layout>
