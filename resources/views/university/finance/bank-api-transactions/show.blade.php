<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Detalhes da Transacção
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $bankTransaction->external_transaction_id }}
                </p>
            </div>

            @if($bankTransaction->type === 'income' && !in_array($bankTransaction->status, ['confirmed']))
            @can('finance.bank-transactions.confirm')
            <a href="{{ route('university.bank-api-transactions.associate.form', $bankTransaction->id) }}" class="inline-flex px-5 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Associar a uma factura
            </a>
            @endcan
            @endif

            <a href="{{ route('university.bank-api-transactions.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="p-4 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            @if(session('warning'))
            <div class="p-4 bg-yellow-100 text-yellow-800 rounded-lg">
                {{ session('warning') }}
            </div>
            @endif

            @if($errors->any())
            <div class="p-4 bg-red-100 text-red-800 rounded-lg">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Valor recebido</p>
                        <h3 class="text-3xl font-bold text-gray-900">
                            {{ number_format($bankTransaction->amount, 2, ',', '.') }}
                            {{ $bankTransaction->currency }}
                        </h3>
                    </div>

                    <span class="px-3 py-1 rounded-full text-sm bg-gray-100 text-gray-700">
                        {{ $bankTransaction->status }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mt-6">
                    <div>
                        <p class="text-sm text-gray-500">Banco / Fornecedor</p>
                        <p class="font-medium">{{ strtoupper($bankTransaction->provider) }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Integração</p>
                        <p class="font-medium">{{ $bankTransaction->integration_name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Conta bancária</p>
                        <p class="font-medium">{{ $bankTransaction->bank_account_name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Tipo</p>
                        <p class="font-medium">
                            {{ $bankTransaction->type === 'income' ? 'Entrada' : 'Saída' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Referência de pagamento</p>
                        <p class="font-medium">{{ $bankTransaction->reference }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">ID externo</p>
                        <p class="font-medium break-all">
                            {{ $bankTransaction->external_transaction_id }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Data da transacção</p>
                        <p class="font-medium">
                            {{ \Carbon\Carbon::parse($bankTransaction->occurred_at)->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Movimento bancário</p>
                        <p class="font-medium">
                            {{ $bankTransaction->movement_reference ?? 'Ainda não criado' }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Descrição</p>
                        <p class="font-medium">{{ $bankTransaction->description }}</p>
                    </div>
                </div>
            </div>

            @if(in_array($bankTransaction->status, ['unmatched', 'failed', 'matched']))
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-2">
                    Confirmar pagamento
                </h3>

                <p class="text-sm text-gray-600 mb-4">
                    O sistema vai procurar uma referência de pagamento válida,
                    verificar o valor e a moeda e confirmar a factura correspondente.
                </p>

                <form method="POST" action="{{ route('university.bank-api-transactions.confirm', $bankTransaction->id) }}" onsubmit="return confirm('Pretende tentar confirmar esta transacção?')">
                    @csrf

                    <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Tentar confirmar pagamento
                    </button>
                </form>
            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">
                    Dados recebidos do banco
                </h3>

                <pre class="text-xs bg-gray-900 text-green-300 p-4 rounded-lg overflow-x-auto">{{ json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    </div>
</x-app-layout>
