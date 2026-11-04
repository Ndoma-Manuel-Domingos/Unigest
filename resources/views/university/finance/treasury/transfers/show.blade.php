<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Transferência {{ $cashTransfer->transfer_number }}</h2>
                <p class="text-sm text-gray-500 mt-1">Detalhes e confirmação da operação.</p>
            </div>

            <a href="{{ route('university.treasury.transfers.index') }}" class="px-4 py-2 bg-gray-100 rounded-lg text-sm text-gray-700 hover:bg-gray-200">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

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

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex flex-wrap justify-between items-start gap-3 mb-6">
                    <div>
                        <p class="text-sm text-gray-500">Valor da transferência</p>
                        <p class="text-3xl font-bold text-gray-900 mt-1">
                            {{ number_format((float) $cashTransfer->amount, 2, ',', '.') }} AOA
                        </p>
                    </div>

                    @php
                    $labels = [
                    'pending' => 'Pendente',
                    'received' => 'Recebida',
                    'rejected' => 'Rejeitada',
                    'cancelled' => 'Cancelada',
                    ];

                    $classes = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'received' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'cancelled' => 'bg-gray-100 text-gray-700',
                    ];
                    @endphp

                    <span class="px-3 py-1.5 rounded-full text-sm {{ $classes[$cashTransfer->status] ?? 'bg-gray-100 text-gray-700' }}">
                        {{ $labels[$cashTransfer->status] ?? $cashTransfer->status }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t pt-5">
                    <div>
                        <p class="text-xs uppercase text-gray-500 font-semibold">Caixa de origem</p>
                        <p class="font-medium text-gray-900 mt-1">
                            {{ $cashTransfer->sourceCashRegister?->user?->name ?? '—' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Caixa #{{ $cashTransfer->source_cash_register_id }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-gray-500 font-semibold">Caixa de destino</p>
                        <p class="font-medium text-gray-900 mt-1">
                            {{ $cashTransfer->destinationCashRegister?->user?->name ?? '—' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Caixa #{{ $cashTransfer->destination_cash_register_id }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-gray-500 font-semibold">Iniciada por</p>
                        <p class="font-medium text-gray-900 mt-1">
                            {{ $cashTransfer->initiator?->name ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-gray-500 font-semibold">Recebida por</p>
                        <p class="font-medium text-gray-900 mt-1">
                            {{ $cashTransfer->receiver?->name ?? 'A aguardar confirmação' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-gray-500 font-semibold">Data de envio</p>
                        <p class="font-medium text-gray-900 mt-1">
                            {{ $cashTransfer->transfer_date?->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase text-gray-500 font-semibold">Data de recepção</p>
                        <p class="font-medium text-gray-900 mt-1">
                            {{ $cashTransfer->received_at?->format('d/m/Y H:i') ?? '—' }}
                        </p>
                    </div>
                </div>

                @if($cashTransfer->notes)
                <div class="mt-6 border-t pt-5">
                    <p class="text-xs uppercase text-gray-500 font-semibold">Observações do envio</p>
                    <p class="text-sm text-gray-700 mt-2">{{ $cashTransfer->notes }}</p>
                </div>
                @endif

                @if($cashTransfer->receiver_notes)
                <div class="mt-5 border-t pt-5">
                    <p class="text-xs uppercase text-gray-500 font-semibold">Observações da recepção/rejeição</p>
                    <p class="text-sm text-gray-700 mt-2">{{ $cashTransfer->receiver_notes }}</p>
                </div>
                @endif
            </div>

            @if($cashTransfer->status === 'pending')
            @if((int) $cashTransfer->destinationCashRegister?->user_id === (int) auth()->id())
            @can('finance.treasury.transfers.receive')
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-900">Confirmar recepção</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Confirma que recebeu fisicamente o valor indicado?
                </p>

                <form method="POST" action="{{ route('university.treasury.transfers.receive', $cashTransfer) }}" class="mt-4 space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Observações (opcional)
                        </label>
                        <textarea name="receiver_notes" rows="2" class="w-full rounded-lg border-gray-300"></textarea>
                    </div>

                    <button type="submit" onclick="return confirm('Confirma que recebeu o dinheiro e pretende actualizar os saldos das duas caixas?')" class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                        Confirmar recepção
                    </button>
                </form>

                <form method="POST" action="{{ route('university.treasury.transfers.reject', $cashTransfer) }}" class="mt-4 border-t pt-4 space-y-3">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Motivo da rejeição
                        </label>
                        <textarea name="receiver_notes" rows="2" required class="w-full rounded-lg border-gray-300"></textarea>
                    </div>

                    <button type="submit" onclick="return confirm('Tem a certeza de que pretende rejeitar esta transferência?')" class="px-5 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                        Rejeitar transferência
                    </button>
                </form>
            </div>
            @endcan
            @endif

            @if((int) $cashTransfer->initiated_by === (int) auth()->id())
            @can('finance.treasury.transfers.create')
            <div class="bg-white rounded-xl shadow-sm p-5">
                <form method="POST" action="{{ route('university.treasury.transfers.cancel', $cashTransfer) }}">
                    @csrf
                    <button type="submit" onclick="return confirm('Pretende cancelar esta transferência pendente?')" class="px-5 py-2 rounded-lg border border-red-300 text-red-700 hover:bg-red-50">
                        Cancelar transferência
                    </button>
                </form>
            </div>
            @endcan
            @endif
            @endif
        </div>
    </div>
</x-app-layout>
