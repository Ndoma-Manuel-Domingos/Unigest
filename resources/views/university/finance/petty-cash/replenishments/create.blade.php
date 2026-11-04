<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Solicitar reposição</h2>
                <p class="text-sm text-gray-500 mt-1">{{ $pettyCashFund->name }}</p>
            </div>

            <a href="{{ route('university.petty-cash.funds.show', $pettyCashFund) }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if($errors->any())
            <div class="p-4 rounded-lg bg-red-50 text-red-700">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Limite autorizado</p>
                    <p class="text-xl font-bold mt-2">
                        {{ number_format((float) $pettyCashFund->authorized_amount, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $pettyCashFund->currency }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Saldo actual</p>
                    <p class="text-xl font-bold text-green-700 mt-2">
                        {{ number_format((float) $pettyCashFund->current_balance, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $pettyCashFund->currency }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Capacidade de reposição</p>
                    <p class="text-xl font-bold text-indigo-700 mt-2">
                        {{ number_format($pettyCashFund->remaining_limit, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $pettyCashFund->currency }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <form method="POST" action="{{ route('university.petty-cash.replenishments.store', $pettyCashFund) }}" class="space-y-5">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Valor solicitado ({{ $pettyCashFund->currency }})
                        </label>
                        <input type="number" name="requested_amount" min="0.01" max="{{ $pettyCashFund->remaining_limit }}" step="0.01" value="{{ old('requested_amount') }}" required class="w-full rounded-lg border-gray-300">
                        @error('requested_amount')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Data do pedido
                        </label>
                        <input type="date" name="request_date" value="{{ old('request_date', now()->toDateString()) }}" required class="w-full rounded-lg border-gray-300">
                        @error('request_date')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Justificação do pedido
                        </label>
                        <textarea name="reason" rows="4" required maxlength="2000" class="w-full rounded-lg border-gray-300" placeholder="Indique por que motivo o fundo necessita de reposição...">{{ old('reason') }}</textarea>
                        @error('reason')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="p-4 rounded-lg bg-blue-50 text-blue-800 text-sm">
                        O pedido será enviado para aprovação. O saldo do fundo não será alterado nesta fase.
                    </div>

                    <div class="flex justify-end gap-3 border-t pt-5">
                        <a href="{{ route('university.petty-cash.funds.show', $pettyCashFund) }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700">
                            Cancelar
                        </a>

                        <button type="submit" class="px-5 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                            Enviar pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
