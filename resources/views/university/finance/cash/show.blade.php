<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Caixa #{{ $cashRegister->id }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Operador: {{ $cashRegister->user->name ?? 'Utilizador removido' }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('university.cash.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg">Voltar</a>

                @if($cashRegister->status === 'open' && $cashRegister->user_id === auth()->id())
                @can('finance.cash.movements.create')
                <a href="{{ route('university.cash.movement.create', $cashRegister) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
                    Registar Movimento
                </a>
                @endcan

                @can('finance.cash.close')
                <a href="{{ route('university.cash.close.create', $cashRegister) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg">
                    Fechar Caixa
                </a>
                @endcan
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="p-4 bg-green-100 text-green-800 rounded-lg">{{ session('success') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Saldo inicial</p>
                    <p class="text-xl font-bold text-gray-800 mt-2">
                        {{ number_format($cashRegister->opening_balance, 2, ',', '.') }} AOA
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Total de entradas</p>
                    <p class="text-xl font-bold text-green-600 mt-2">
                        {{ number_format($income, 2, ',', '.') }} AOA
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Total de saídas</p>
                    <p class="text-xl font-bold text-red-600 mt-2">
                        {{ number_format($expense, 2, ',', '.') }} AOA
                    </p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">
                        {{ $cashRegister->status === 'open' ? 'Saldo esperado' : 'Saldo contado' }}
                    </p>
                    <p class="text-xl font-bold text-indigo-600 mt-2">
                        {{ number_format(
                            $cashRegister->status === 'open'
                                ? $expectedBalance
                                : $cashRegister->counted_balance,
                            2, ',', '.'
                        ) }} AOA
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex flex-wrap justify-between gap-3">
                    <div>
                        <p class="text-sm text-gray-500">Aberto em</p>
                        <p class="font-medium">{{ $cashRegister->opened_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Fechado em</p>
                        <p class="font-medium">{{ $cashRegister->closed_at?->format('d/m/Y H:i') ?? 'Ainda aberto' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Estado</p>
                        @if($cashRegister->status === 'open')
                        <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-700">Aberto</span>
                        @else
                        <span class="px-3 py-1 text-sm rounded-full bg-gray-100 text-gray-700">Fechado</span>
                        @endif
                    </div>

                    @if($cashRegister->status === 'closed')
                    <div>
                        <p class="text-sm text-gray-500">Diferença</p>
                        <p class="font-bold {{ $cashRegister->difference == 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($cashRegister->difference, 2, ',', '.') }} AOA
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-gray-800">Movimentos do Caixa</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descrição</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Origem</th>
                                <th class="px-5 py-3 text-left text-xs font-medium text-gray-500 uppercase">Operador</th>
                                <th class="px-5 py-3 text-right text-xs font-medium text-gray-500 uppercase">Valor</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($cashRegister->movements as $movement)
                            <tr>
                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ $movement->movement_date->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-800">
                                    {{ $movement->description }}
                                    @if($movement->payment)
                                    <p class="text-xs text-gray-500 mt-1">
                                        Recibo: {{ $movement->payment->payment_number }}
                                    </p>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600">
                                    @switch($movement->source)
                                    @case('payment') Pagamento @break
                                    @case('payment_reversal') Reversão @break
                                    @default Manual
                                    @endswitch
                                </td>
                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ $movement->user->name ?? '—' }}
                                </td>
                                <td class="px-5 py-4 text-right font-semibold {{ $movement->type === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->type === 'income' ? '+' : '-' }}
                                    {{ number_format($movement->amount, 2, ',', '.') }} AOA
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-8 text-center text-gray-500">
                                    Não existem movimentos neste caixa.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($cashRegister->status === 'closed')
            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Resumo do Fecho</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Saldo esperado</p>
                        <p class="font-bold">{{ number_format($cashRegister->expected_balance, 2, ',', '.') }} AOA</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Saldo contado</p>
                        <p class="font-bold">{{ number_format($cashRegister->counted_balance, 2, ',', '.') }} AOA</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Diferença</p>
                        <p class="font-bold">{{ number_format($cashRegister->difference, 2, ',', '.') }} AOA</p>
                    </div>
                </div>

                @if($cashRegister->closing_notes)
                <p class="mt-4 text-sm text-gray-600">
                    <strong>Observações:</strong> {{ $cashRegister->closing_notes }}
                </p>
                @endif
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
