<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Fecho Diário
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $closure->closure_date->format('d/m/Y') }}
                </p>
            </div>

            <a href="{{ route('university.treasury.daily-closures.index') }}" class="px-4 py-2 rounded-lg border border-gray-300 text-gray-700 hover:bg-gray-50">
                Voltar
            </a>

        </div>
    </x-slot>

    <div class="py-6">

        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">

                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <p class="text-sm text-gray-500">
                        Saldo Inicial
                    </p>

                    <p class="text-2xl font-bold mt-2">
                        {{ number_format($closure->opening_balance, 2, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <p class="text-sm text-gray-500">
                        Entradas
                    </p>

                    <p class="text-2xl font-bold text-green-600 mt-2">
                        {{ number_format($closure->total_income, 2, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <p class="text-sm text-gray-500">
                        Saídas
                    </p>

                    <p class="text-2xl font-bold text-red-600 mt-2">
                        {{ number_format($closure->total_expense, 2, ',', '.') }}
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm border p-5">
                    <p class="text-sm text-gray-500">
                        Saldo Esperado
                    </p>

                    <p class="text-2xl font-bold text-blue-600 mt-2">
                        {{ number_format($closure->expected_balance, 2, ',', '.') }}
                    </p>
                </div>

            </div>

            <div class="mt-6 bg-white rounded-xl shadow-sm border p-6">

                <h3 class="text-lg font-semibold text-gray-800 mb-6">
                    Conferência
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div>
                        <p class="text-sm text-gray-500">
                            Saldo Esperado
                        </p>

                        <p class="text-xl font-semibold mt-1">
                            {{ number_format($closure->expected_balance, 2, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Saldo Contado
                        </p>

                        <p class="text-xl font-semibold mt-1">
                            {{ number_format($closure->counted_balance, 2, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Diferença
                        </p>

                        <p class="text-xl font-bold mt-1
                            {{ $closure->difference == 0
                                ? 'text-green-600'
                                : 'text-red-600' }}">
                            {{ number_format($closure->difference, 2, ',', '.') }}
                        </p>
                    </div>

                </div>

            </div>

            <div class="mt-6 bg-white rounded-xl shadow-sm border p-6">

                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    Responsável pelo Fecho
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <p class="text-sm text-gray-500">
                            Utilizador
                        </p>

                        <p class="font-medium mt-1">
                            {{ $closure->closedBy?->name ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Data/Hora
                        </p>

                        <p class="font-medium mt-1">
                            {{ $closure->closed_at?->format('d/m/Y H:i:s') ?? '—' }}
                        </p>
                    </div>

                </div>

                @if($closure->notes)

                <div class="mt-6">

                    <p class="text-sm text-gray-500">
                        Observações
                    </p>

                    <div class="mt-2 rounded-lg bg-gray-50 border p-4 text-gray-700">
                        {{ $closure->notes }}
                    </div>

                </div>

                @endif

            </div>

            <div class="mt-6">

                @if($closure->difference == 0)

                <div class="rounded-xl bg-green-50 border border-green-200 p-5 text-green-800">
                    <strong>Fecho conferido.</strong>
                    O saldo contado corresponde ao saldo esperado.
                </div>

                @elseif($closure->difference > 0)

                <div class="rounded-xl bg-blue-50 border border-blue-200 p-5 text-blue-800">
                    <strong>Existência de sobra.</strong>
                    O valor contado é superior ao saldo esperado.
                </div>

                @else

                <div class="rounded-xl bg-red-50 border border-red-200 p-5 text-red-800">
                    <strong>Existência de falta.</strong>
                    O valor contado é inferior ao saldo esperado.
                </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>
