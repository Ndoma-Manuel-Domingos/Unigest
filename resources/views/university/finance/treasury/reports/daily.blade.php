<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Relatório Diário da Tesouraria
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Consulta consolidada dos movimentos e do fecho diário.
                </p>
            </div>

            <a href="{{ route('university.treasury.reports.daily.print', ['date' => $date]) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900">
                Imprimir
            </a>

        </div>

    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Filtro --}}
            <div class="bg-white rounded-xl shadow-sm border p-5 mb-6">

                <form method="GET" action="{{ route('university.treasury.reports.daily') }}" class="flex flex-col sm:flex-row sm:items-end gap-4">

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Data
                        </label>

                        <input type="date" name="date" value="{{ $date }}" class="rounded-lg border-gray-300" required>
                    </div>

                    <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                        Consultar
                    </button>

                </form>

            </div>

            {{-- Estado do fecho --}}
            @if(!$closure)

            <div class="mb-6 rounded-xl bg-yellow-50 border border-yellow-200 p-5 text-yellow-800">

                <div class="flex items-center justify-between gap-4">

                    <div>
                        <p class="font-semibold">
                            O dia ainda não foi fechado.
                        </p>

                        <p class="text-sm mt-1">
                            Não existe um fecho diário registado para
                            {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}.
                        </p>
                    </div>

                    @can('finance.treasury.closures.create')

                    <a href="{{ route('university.treasury.daily-closures.create', ['date' => $date]) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        Fechar Dia
                    </a>

                    @endcan

                </div>

            </div>

            @else

            <div class="mb-6 rounded-xl bg-green-50 border border-green-200 p-5">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="font-semibold text-green-800">
                            Dia fechado
                        </p>

                        <p class="text-sm text-green-700 mt-1">
                            Fechado por:
                            {{ $closure->closedBy?->name ?? '—' }}
                            em
                            {{ $closure->closed_at?->format('d/m/Y H:i') ?? '—' }}
                        </p>
                    </div>

                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-sm font-semibold">
                        Fechado
                    </span>

                </div>

            </div>

            @endif

            {{-- Resumo --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-5">

                <div class="bg-white rounded-xl shadow-sm border p-5">

                    <p class="text-sm text-gray-500">
                        Saldo Inicial
                    </p>

                    <p class="text-2xl font-bold mt-2">
                        {{ number_format($closure?->opening_balance ?? 0, 2, ',', '.') }}
                    </p>

                </div>

                <div class="bg-white rounded-xl shadow-sm border p-5">

                    <p class="text-sm text-gray-500">
                        Entradas
                    </p>

                    <p class="text-2xl font-bold text-green-600 mt-2">
                        {{ number_format($income, 2, ',', '.') }}
                    </p>

                </div>

                <div class="bg-white rounded-xl shadow-sm border p-5">

                    <p class="text-sm text-gray-500">
                        Saídas
                    </p>

                    <p class="text-2xl font-bold text-red-600 mt-2">
                        {{ number_format($expense, 2, ',', '.') }}
                    </p>

                </div>

                <div class="bg-white rounded-xl shadow-sm border p-5">

                    <p class="text-sm text-gray-500">
                        Saldo Esperado
                    </p>

                    <p class="text-2xl font-bold text-blue-600 mt-2">
                        {{ number_format($closure?->expected_balance ?? (($closure?->opening_balance ?? 0) + $income - $expense), 2, ',', '.') }}
                    </p>

                </div>

                <div class="bg-white rounded-xl shadow-sm border p-5">

                    <p class="text-sm text-gray-500">
                        Diferença
                    </p>

                    <p class="text-2xl font-bold
                        {{ !$closure || $closure->difference == 0
                            ? 'text-green-600'
                            : 'text-red-600' }} mt-2">

                        {{ number_format($closure?->difference ?? 0, 2, ',', '.') }}

                    </p>

                </div>

            </div>

            {{-- Fecho --}}
            @if($closure)

            <div class="bg-white rounded-xl shadow-sm border p-6 mt-6">

                <h3 class="text-lg font-semibold text-gray-800 mb-5">
                    Conferência do Fecho
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div>
                        <p class="text-sm text-gray-500">
                            Saldo Esperado
                        </p>

                        <p class="text-xl font-bold mt-1">
                            {{ number_format($closure->expected_balance, 2, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Saldo Contado
                        </p>

                        <p class="text-xl font-bold mt-1">
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

                @if($closure->notes)

                <div class="mt-6">

                    <p class="text-sm text-gray-500 mb-2">
                        Observações
                    </p>

                    <div class="rounded-lg bg-gray-50 border p-4">
                        {{ $closure->notes }}
                    </div>

                </div>

                @endif

            </div>

            @endif

            {{-- Movimentos --}}
            <div class="bg-white rounded-xl shadow-sm border mt-6 overflow-hidden">

                <div class="px-6 py-5 border-b">

                    <div class="flex items-center justify-between">

                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Movimentos do Dia
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ $movements->count() }} movimento(s)
                            </p>
                        </div>

                    </div>

                </div>

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Hora
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Tipo
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Descrição
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                    Operador
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                    Valor
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse($movements as $movement)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ \Carbon\Carbon::parse($movement->movement_date)->format('H:i') }}
                                </td>

                                <td class="px-6 py-4">

                                    @if($movement->type === 'income')

                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                        Entrada
                                    </span>

                                    @else

                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                        Saída
                                    </span>

                                    @endif

                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">
                                        {{ $movement->description }}
                                    </div>

                                    @if($movement->reference)
                                    <div class="text-xs text-gray-500 mt-1">
                                        Ref.: {{ $movement->reference }}
                                    </div>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    {{ $movement->user?->name ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-right font-semibold
                                        {{ $movement->type === 'income'
                                            ? 'text-green-600'
                                            : 'text-red-600' }}">

                                    {{ number_format($movement->amount, 2, ',', '.') }}

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">

                                    Não existem movimentos para esta data.

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
