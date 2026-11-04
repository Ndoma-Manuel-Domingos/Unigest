<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Pedido {{ $replenishment->request_number }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Fundo: {{ $replenishment->fund?->name }}
                </p>
            </div>

            <a href="{{ route('university.petty-cash.replenishments.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">
                Voltar aos pedidos
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="p-4 rounded-lg bg-green-50 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="p-4 rounded-lg bg-red-50 text-red-700">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            @php
            $statusLabels = [
            'pending' => 'Pendente',
            'approved' => 'Aprovado',
            'rejected' => 'Rejeitado',
            'paid' => 'Pago',
            'cancelled' => 'Cancelado',
            ];
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Valor solicitado</p>
                    <p class="text-2xl font-bold mt-2">
                        {{ number_format((float) $replenishment->requested_amount, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $replenishment->fund?->currency }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Valor aprovado</p>
                    <p class="text-2xl font-bold text-indigo-700 mt-2">
                        {{ $replenishment->approved_amount !== null
                            ? number_format((float) $replenishment->approved_amount, 2, ',', '.')
                            : '—' }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $replenishment->fund?->currency }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Estado</p>
                    <p class="text-xl font-bold mt-2">
                        {{ $statusLabels[$replenishment->status] ?? $replenishment->status }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">Dados do pedido</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 text-sm">
                    <div>
                        <p class="text-gray-500">Fundo</p>
                        <p class="font-medium text-gray-900">{{ $replenishment->fund?->name }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Responsável pelo pedido</p>
                        <p class="font-medium text-gray-900">{{ $replenishment->requester?->name }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Data do pedido</p>
                        <p class="font-medium text-gray-900">{{ $replenishment->request_date?->format('d/m/Y') }}</p>
                    </div>

                    <div>
                        <p class="text-gray-500">Aprovado por</p>
                        <p class="font-medium text-gray-900">{{ $replenishment->reviewer?->name ?? '—' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-gray-500">Justificação</p>
                        <p class="font-medium text-gray-900 whitespace-pre-line">{{ $replenishment->reason }}</p>
                    </div>

                    @if($replenishment->review_notes)
                    <div class="md:col-span-2">
                        <p class="text-gray-500">Observações da aprovação/rejeição</p>
                        <p class="font-medium text-gray-900 whitespace-pre-line">{{ $replenishment->review_notes }}</p>
                    </div>
                    @endif

                    @if($replenishment->paid_at)
                    <div>
                        <p class="text-gray-500">Data do pagamento</p>
                        <p class="font-medium text-gray-900">{{ $replenishment->paid_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif

                    @if($replenishment->movement)
                    <div>
                        <p class="text-gray-500">Movimento gerado</p>
                        <p class="font-medium text-gray-900">{{ $replenishment->movement->movement_number }}</p>
                    </div>
                    @endif
                </div>
            </div>

            @if($replenishment->status === 'pending')
            @can('finance.petty-cash.replenishments.approve')
            @if((int) $replenishment->requested_by !== (int) auth()->id())
            <div class="bg-white rounded-xl shadow-sm p-6 space-y-6">
                <div>
                    <h3 class="font-semibold text-gray-800">Aprovar pedido</h3>

                    <form method="POST" action="{{ route('university.petty-cash.replenishments.approve', $replenishment) }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Valor a aprovar ({{ $replenishment->fund?->currency }})
                            </label>
                            <input type="number" name="approved_amount" min="0.01" max="{{ $replenishment->requested_amount }}" step="0.01" value="{{ old('approved_amount', $replenishment->requested_amount) }}" required class="w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Observações
                            </label>
                            <textarea name="review_notes" rows="2" class="w-full rounded-lg border-gray-300">{{ old('review_notes') }}</textarea>
                        </div>

                        <button type="submit" class="px-5 py-2 rounded-lg bg-green-600 text-white">
                            Aprovar pedido
                        </button>
                    </form>
                </div>

                <div class="border-t pt-5">
                    <h3 class="font-semibold text-gray-800">Rejeitar pedido</h3>

                    <form method="POST" action="{{ route('university.petty-cash.replenishments.reject', $replenishment) }}" class="mt-4 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Motivo da rejeição
                            </label>
                            <textarea name="review_notes" rows="3" required class="w-full rounded-lg border-gray-300"></textarea>
                        </div>

                        <button type="submit" onclick="return confirm('Confirma a rejeição deste pedido?')" class="px-5 py-2 rounded-lg bg-red-600 text-white">
                            Rejeitar pedido
                        </button>
                    </form>
                </div>
            </div>
            @endif
            @endcan
            @endif

            @if($replenishment->status === 'approved')
            @can('finance.petty-cash.replenishments.pay')
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800">Pagamento da reposição</h3>

                <p class="text-sm text-gray-600 mt-2">
                    Confirme apenas depois de entregar efectivamente o dinheiro ao responsável.
                </p>

                <div class="mt-4 p-4 rounded-lg bg-blue-50 text-blue-800">
                    Valor a entregar:
                    <strong>
                        {{ number_format((float) $replenishment->approved_amount, 2, ',', '.') }}
                        {{ $replenishment->fund?->currency }}
                    </strong>
                </div>

                <form method="POST" action="{{ route('university.petty-cash.replenishments.pay', $replenishment) }}" class="mt-5">
                    @csrf

                    <button type="submit" onclick="return confirm('Confirma que o dinheiro foi efectivamente entregue ao responsável?')" class="px-5 py-2 rounded-lg bg-indigo-600 text-white">
                        Confirmar entrega e actualizar saldo
                    </button>
                </form>
            </div>
            @endcan
            @endif

        </div>
    </div>
</x-app-layout>
