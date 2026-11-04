<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Associar Transacção a Factura
                </h2>
                <p class="text-sm text-gray-500">
                    Seleccione a factura correspondente à transferência recebida.
                </p>
            </div>

            <a href="{{ route('university.bank-api-transactions.show', $bankTransaction->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

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
                <h3 class="font-semibold text-gray-800 mb-4">
                    Dados da transferência
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <p class="text-sm text-gray-500">Referência</p>
                        <p class="font-semibold">
                            {{ $bankTransaction->reference }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">ID da transacção</p>
                        <p class="font-semibold break-all">
                            {{ $bankTransaction->external_transaction_id }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Valor recebido</p>
                        <p class="text-xl font-bold text-gray-900">
                            {{ number_format($bankTransaction->amount, 2, ',', '.') }}
                            {{ $bankTransaction->currency }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Data</p>
                        <p class="font-semibold">
                            {{ \Carbon\Carbon::parse($bankTransaction->occurred_at)->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Descrição</p>
                        <p>{{ $bankTransaction->description }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6">
                <form method="POST" action="{{ route('university.bank-api-transactions.associate', $bankTransaction->id) }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="invoice_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Factura
                        </label>

                        <select name="invoice_id" id="invoice_id" required class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Seleccione uma factura</option>

                            @foreach($invoices as $invoice)
                            <option value="{{ $invoice->id }}" @selected(old('invoice_id')==$invoice->id)>
                                {{ $invoice->invoice_number }}
                                — Saldo:
                                {{ number_format($invoice->balance, 2, ',', '.') }}
                                {{ $invoice->currency }}
                            </option>
                            @endforeach
                        </select>

                        @if($invoices->isEmpty())
                        <p class="mt-2 text-sm text-red-600">
                            Não existem facturas com saldo em dívida disponíveis.
                        </p>
                        @endif
                    </div>

                    <div>
                        <label for="observations" class="block text-sm font-medium text-gray-700 mb-2">
                            Observações
                        </label>

                        <textarea name="observations" id="observations" rows="3" maxlength="1000" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Motivo ou informação adicional da associação">{{ old('observations') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('university.bank-api-transactions.show', $bankTransaction->id) }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">
                            Cancelar
                        </a>

                        <button type="submit" @disabled($invoices->isEmpty())
                            class="px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                            Associar e confirmar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
