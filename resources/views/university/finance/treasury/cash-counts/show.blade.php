<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">
                    Contagem {{ $cashCount->reference }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Caixa #{{ $cashCount->cash_register_id }} —
                    {{ $cashCount->counter?->name ?? 'Operador' }}
                </p>
            </div>

            <a href="{{ route('university.treasury.cash-counts.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="rounded-lg bg-green-50 border border-green-200 p-4 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="rounded-lg bg-red-50 border border-red-200 p-4 text-red-700">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Saldo esperado</p>
                    <p class="text-2xl font-bold mt-2 text-gray-900">
                        {{ number_format((float) $cashCount->expected_balance, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">AOA</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Saldo contado</p>
                    <p class="text-2xl font-bold mt-2 text-indigo-700">
                        {{ number_format((float) $cashCount->counted_balance, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">AOA</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Diferença</p>
                    <p class="text-2xl font-bold mt-2
                        {{ (float) $cashCount->difference < 0 ? 'text-red-700' : ((float) $cashCount->difference > 0 ? 'text-green-700' : 'text-gray-900') }}">
                        {{ number_format((float) $cashCount->difference, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">AOA</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-gray-800">Discriminação das notas e moedas</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Contagem efectuada em {{ $cashCount->counted_at?->format('d/m/Y H:i') }}.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Denominação</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Quantidade</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($cashCount->denominations ?? [] as $item)
                            <tr>
                                <td class="px-5 py-3 text-sm text-gray-800">
                                    {{ number_format($item['value'], 0, ',', '.') }} AOA
                                </td>
                                <td class="px-5 py-3 text-right text-sm text-gray-700">
                                    {{ number_format($item['quantity'], 0, ',', '.') }}
                                </td>
                                <td class="px-5 py-3 text-right text-sm font-medium text-gray-900">
                                    {{ number_format($item['subtotal'], 2, ',', '.') }} AOA
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Estado da análise</h3>

                <p class="text-sm text-gray-700">
                    Estado:
                    <strong>
                        {{ [
                            'pending' => 'Pendente',
                            'approved' => 'Aprovada',
                            'rejected' => 'Rejeitada',
                        ][$cashCount->status] ?? $cashCount->status }}
                    </strong>
                </p>

                @if($cashCount->notes)
                <p class="text-sm text-gray-600 mt-3">
                    <strong>Observações do operador:</strong>
                    {{ $cashCount->notes }}
                </p>
                @endif

                @if($cashCount->reviewer)
                <p class="text-sm text-gray-600 mt-3">
                    <strong>Analisada por:</strong> {{ $cashCount->reviewer->name }}
                    em {{ $cashCount->reviewed_at?->format('d/m/Y H:i') }}
                </p>
                @endif

                @if($cashCount->review_notes)
                <p class="text-sm text-gray-600 mt-2">
                    <strong>Observações da análise:</strong>
                    {{ $cashCount->review_notes }}
                </p>
                @endif
            </div>

            @if($cashCount->status === 'pending' && (int) $cashCount->counted_by !== (int) auth()->id())
            @can('finance.treasury.cash-counts.approve')
            <div class="bg-white rounded-xl shadow-sm p-5 space-y-5">
                <form method="POST" action="{{ route('university.treasury.cash-counts.approve', $cashCount) }}" class="space-y-3">
                    @csrf

                    <label class="block text-sm font-medium text-gray-700">
                        Observações da aprovação
                    </label>

                    <textarea name="review_notes" rows="2" class="w-full rounded-lg border-gray-300" placeholder="Observações opcionais"></textarea>

                    <button type="submit" onclick="return confirm('Confirma a aprovação desta contagem física?')" class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                        Aprovar contagem
                    </button>
                </form>

                <form method="POST" action="{{ route('university.treasury.cash-counts.reject', $cashCount) }}" class="border-t pt-5 space-y-3">
                    @csrf

                    <label class="block text-sm font-medium text-gray-700">
                        Motivo da rejeição
                    </label>

                    <textarea name="review_notes" rows="2" required class="w-full rounded-lg border-gray-300"></textarea>

                    <button type="submit" onclick="return confirm('Confirma a rejeição desta contagem?')" class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                        Rejeitar contagem
                    </button>
                </form>
            </div>
            @endcan
            @endif
        </div>
    </div>
</x-app-layout>
