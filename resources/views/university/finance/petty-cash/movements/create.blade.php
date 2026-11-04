<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Registar despesa</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Fundo: {{ $pettyCashFund->name }} ({{ $pettyCashFund->code }})
                </p>
            </div>

            <a href="{{ route('university.petty-cash.funds.show', $pettyCashFund) }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">
                Voltar ao fundo
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if($errors->any())
            <div class="p-4 rounded-lg bg-red-50 border border-red-200 text-red-700">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-5">
                <p class="text-sm text-gray-500">Saldo disponível</p>
                <p class="text-2xl font-bold text-green-700 mt-1">
                    {{ number_format((float) $pettyCashFund->current_balance, 2, ',', '.') }}
                    {{ $pettyCashFund->currency }}
                </p>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <form method="POST" action="{{ route('university.petty-cash.movements.store', $pettyCashFund) }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Descrição da despesa</label>
                        <input type="text" name="description" value="{{ old('description') }}" required maxlength="255" class="w-full rounded-lg border-gray-300">
                        @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Valor ({{ $pettyCashFund->currency }})</label>
                            <input type="number" name="amount" min="0.01" step="0.01" value="{{ old('amount') }}" required class="w-full rounded-lg border-gray-300">
                            @error('amount') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data da despesa</label>
                            <input type="date" name="movement_date" value="{{ old('movement_date', now()->toDateString()) }}" required class="w-full rounded-lg border-gray-300">
                            @error('movement_date') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fornecedor</label>
                            <input type="text" name="supplier_name" value="{{ old('supplier_name') }}" maxlength="150" class="w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">N.º do documento</label>
                            <input type="text" name="document_number" value="{{ old('document_number') }}" maxlength="100" class="w-full rounded-lg border-gray-300">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Comprovativo (PDF, JPG ou PNG)
                        </label>
                        <input type="file" name="receipt" accept=".pdf,.jpg,.jpeg,.png" class="w-full rounded-lg border border-gray-300 p-2">
                        <p class="text-xs text-gray-500 mt-1">Tamanho máximo: 5 MB.</p>
                        @error('receipt') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observações</label>
                        <textarea name="notes" rows="3" class="w-full rounded-lg border-gray-300">{{ old('notes') }}</textarea>
                    </div>

                    <div class="rounded-lg bg-yellow-50 border border-yellow-200 p-4 text-sm text-yellow-800">
                        A despesa ficará pendente. O saldo só será reduzido depois da aprovação por outro utilizador autorizado.
                    </div>

                    <div class="flex justify-end gap-3 border-t pt-5">
                        <a href="{{ route('university.petty-cash.funds.show', $pettyCashFund) }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700">
                            Cancelar
                        </a>

                        <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                            Submeter despesa
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
