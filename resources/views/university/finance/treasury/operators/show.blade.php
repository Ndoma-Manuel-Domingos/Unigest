<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detalhe da Caixa #{{ $cashRegister->id }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Operador: {{ $cashRegister->user?->name ?? 'Utilizador removido' }}
                </p>
            </div>

            <a href="{{ route('university.treasury.operators.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 text-sm">
                Voltar à listagem
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Saldo inicial</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">
                        {{ number_format((float) $cashRegister->opening_balance, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">AOA</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Total de entradas</p>
                    <p class="text-2xl font-bold text-green-700 mt-2">
                        {{ number_format((float) $totalIncome, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">AOA</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Total de saídas</p>
                    <p class="text-2xl font-bold text-red-700 mt-2">
                        {{ number_format((float) $totalExpense, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">AOA</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Saldo esperado</p>
                    <p class="text-2xl font-bold text-indigo-700 mt-2">
                        {{ number_format($expectedBalance, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">AOA</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4">Dados da sessão</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-sm">
                    <div>
                        <p class="text-gray-500">Operador</p>
                        <p class="font-medium text-gray-900">
                            {{ $cashRegister->user?->name ?? 'Utilizador removido' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Data de abertura</p>
                        <p class="font-medium text-gray-900">
                            {{ $cashRegister->opened_at?->format('d/m/Y H:i') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Data de fecho</p>
                        <p class="font-medium text-gray-900">
                            {{ $cashRegister->closed_at?->format('d/m/Y H:i') ?? 'Ainda aberta' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Saldo contado</p>
                        <p class="font-medium text-gray-900">
                            {{ $cashRegister->counted_balance !== null
                                ? number_format((float) $cashRegister->counted_balance, 2, ',', '.') . ' AOA'
                                : 'Não registado' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Diferença no fecho</p>
                        <p class="font-semibold {{ (float) $cashRegister->difference < 0 ? 'text-red-700' : 'text-green-700' }}">
                            {{ $cashRegister->difference !== null
                                ? number_format((float) $cashRegister->difference, 2, ',', '.') . ' AOA'
                                : 'Ainda não calculada' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-gray-500">Estado</p>
                        @if($cashRegister->status === 'open')
                        <span class="inline-flex mt-1 px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs">
                            Aberta
                        </span>
                        @else
                        <span class="inline-flex mt-1 px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-xs">
                            Fechada
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-gray-800">Movimentos da caixa</h3>
                    <p class="text-sm text-gray-500">
                        Histórico de entradas e saídas registadas nesta sessão.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Descrição</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Operador</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Valor</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($movements as $movement)
                            <tr>
                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $movement->movement_date?->format('d/m/Y H:i') ?? '—' }}
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-900">
                                    {{ $movement->description }}
                                    @if($movement->observations)
                                    <p class="text-xs text-gray-500 mt-1">{{ $movement->observations }}</p>
                                    @endif
                                </td>

                                <td class="px-5 py-4">
                                    @if($movement->type === 'income')
                                    <span class="px-2.5 py-1 rounded-full bg-green-100 text-green-700 text-xs">
                                        Entrada
                                    </span>
                                    @else
                                    <span class="px-2.5 py-1 rounded-full bg-red-100 text-red-700 text-xs">
                                        Saída
                                    </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $movement->user?->name ?? '—' }}
                                </td>

                                <td class="px-5 py-4 text-right font-medium text-sm
                                        {{ $movement->type === 'income' ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $movement->type === 'income' ? '+' : '-' }}
                                    {{ number_format((float) $movement->amount, 2, ',', '.') }} AOA
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-10 text-center text-gray-500">
                                    Esta caixa ainda não tem movimentos registados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-5 border-t">
                    {{ $movements->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
