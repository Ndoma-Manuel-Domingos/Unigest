<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Fecho Diário da Tesouraria
            </h2>

            <p class="text-sm text-gray-500 mt-1">
                Confirmar os valores do dia.
            </p>
        </div>
    </x-slot>

    <div class="py-6">

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-4">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('university.treasury.daily-closures.store') }}">

                @csrf

                <div class="bg-white shadow-sm rounded-xl p-6">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Data
                            </label>

                            <input type="date" name="closure_date" value="{{ old('closure_date', $date) }}" class="w-full rounded-lg border-gray-300" required>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Saldo Inicial
                            </label>

                            <input type="number" step="0.01" min="0" name="opening_balance" value="{{ old('opening_balance', $openingBalance) }}" class="w-full rounded-lg border-gray-300" required>
                        </div>

                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">

                        <div class="rounded-xl bg-green-50 border border-green-200 p-5">
                            <p class="text-sm text-green-700">
                                Entradas
                            </p>

                            <p class="text-2xl font-bold text-green-800 mt-2">
                                {{ number_format($income, 2, ',', '.') }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-red-50 border border-red-200 p-5">
                            <p class="text-sm text-red-700">
                                Saídas
                            </p>

                            <p class="text-2xl font-bold text-red-800 mt-2">
                                {{ number_format($expense, 2, ',', '.') }}
                            </p>
                        </div>

                        <div class="rounded-xl bg-blue-50 border border-blue-200 p-5">
                            <p class="text-sm text-blue-700">
                                Saldo Esperado
                            </p>

                            <p class="text-2xl font-bold text-blue-800 mt-2">
                                {{ number_format($expectedBalance, 2, ',', '.') }}
                            </p>
                        </div>

                    </div>

                    <div class="mt-6 rounded-lg bg-gray-50 border p-4">
                        <p class="text-sm text-gray-600">
                            Movimentos encontrados no dia:
                            <strong>{{ $movementsCount }}</strong>
                        </p>
                    </div>

                    <div class="mt-6">

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Saldo Contado
                        </label>

                        <input type="number" step="0.01" min="0" name="counted_balance" value="{{ old('counted_balance') }}" class="w-full rounded-lg border-gray-300" required>

                        <p class="text-xs text-gray-500 mt-1">
                            Informe o valor físico efetivamente contado.
                        </p>

                    </div>

                    <div class="mt-6">

                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Observações
                        </label>

                        <textarea name="notes" rows="4" class="w-full rounded-lg border-gray-300" placeholder="Observações do fecho...">{{ old('notes') }}</textarea>

                    </div>

                    <div class="mt-8 flex items-center justify-between">

                        <a href="{{ route('university.treasury.daily-closures.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </a>

                        <button type="submit" class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                            Confirmar Fecho Diário
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</x-app-layout>
