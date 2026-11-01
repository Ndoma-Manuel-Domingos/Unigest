<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Abertura do Caixa
            </h2>
            <p class="text-sm text-gray-500">
                Registe o saldo inicial para iniciar as operações.
            </p>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
            <div class="mb-5 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700">
                <ul class="list-disc list-inside text-sm space-y-1">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-5 p-4 rounded-lg bg-red-50 border border-red-200 text-red-700">
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

                <div class="p-5 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800">
                        Dados da abertura
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Preencha os dados para abrir o caixa.
                    </p>
                </div>

                <form action="{{ route('university.cash.store') }}" method="POST">
                    @csrf

                    <div class="p-6 space-y-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Operador
                            </label>
                            <input type="text" value="{{ auth()->user()->name }}" disabled class="w-full rounded-lg border-gray-300 bg-gray-100">
                        </div>


                        <div>
                            <label for="opening_balance" class="block text-sm font-medium text-gray-700 mb-2">
                                Saldo inicial (AOA) <span class="text-red-500">*</span>
                            </label>

                            <input type="number" name="opening_balance" id="opening_balance" step="0.01" min="0" value="{{ old('opening_balance', 0) }}" required class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Ex.: 50000.00">

                            @error('opening_balance')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="opening_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Observações
                            </label>

                            <textarea name="opening_notes" id="opening_notes" rows="4" class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500" placeholder="Observações sobre a abertura do caixa...">{{ old('opening_notes') }}</textarea>

                            @error('opening_notes')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="rounded-lg bg-blue-50 border border-blue-100 p-4">
                            <div class="flex gap-3">
                                <i class="fas fa-info-circle text-blue-600 mt-1"></i>
                                <div class="text-sm text-blue-800">
                                    <p class="font-semibold">Importante</p>
                                    <p class="mt-1">
                                        Confirme o dinheiro disponível antes de iniciar
                                        as operações. O saldo inicial será utilizado
                                        no cálculo do fecho do caixa.
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">

                        <a href="{{ route('university.cash.index') }}" class="inline-flex justify-center items-center px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-100">
                            Cancelar
                        </a>

                        <button type="submit" class="inline-flex justify-center items-center px-5 py-2.5 rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-lock-open mr-2"></i>
                            Abrir Caixa
                        </button>

                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>
