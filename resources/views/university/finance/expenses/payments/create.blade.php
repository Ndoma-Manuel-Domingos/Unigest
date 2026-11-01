<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Registar pagamento
                </h2>
                <p class="text-sm text-gray-500">
                    Despesa {{ $expense->expense_number }}
                </p>
            </div>

            <a href="{{ route('university.expenses.show', $expense) }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm text-gray-700 hover:bg-gray-300">
                Voltar à despesa
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if ($errors->any())
            <div class="rounded-lg bg-red-100 p-4 text-red-700">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-sm text-gray-500">Valor da despesa</p>
                    <p class="mt-2 text-xl font-bold">
                        {{ number_format($expense->amount, 2, ',', '.') }}
                        {{ $expense->currency }}
                    </p>
                </div>

                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-sm text-gray-500">Já pago</p>
                    <p class="mt-2 text-xl font-bold text-green-600">
                        {{ number_format($expense->paid_amount, 2, ',', '.') }}
                        {{ $expense->currency }}
                    </p>
                </div>

                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-sm text-gray-500">Saldo em dívida</p>
                    <p class="mt-2 text-xl font-bold text-red-600">
                        {{ number_format($expense->balance, 2, ',', '.') }}
                        {{ $expense->currency }}
                    </p>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow">
                <form method="POST" action="{{ route('university.expenses.payments.store', $expense) }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Data do pagamento *
                            </label>
                            <input type="datetime-local" name="payment_date" value="{{ old('payment_date', now()->format('Y-m-d\TH:i')) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Valor a pagar *
                            </label>
                            <input type="number" name="amount" min="0.01" max="{{ $expense->balance }}" step="0.01" value="{{ old('amount', $expense->balance) }}" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="mt-1 text-xs text-gray-500">
                                Não pode ultrapassar o saldo de
                                {{ number_format($expense->balance, 2, ',', '.') }}
                                {{ $expense->currency }}.
                            </p>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Método de pagamento *
                            </label>
                            <select name="payment_method" required class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Seleccione</option>
                                <option value="cash" @selected(old('payment_method')==='cash' )>
                                    Dinheiro
                                </option>
                                <option value="bank_transfer" @selected(old('payment_method')==='bank_transfer' )>
                                    Transferência bancária
                                </option>
                                <option value="multicaixa" @selected(old('payment_method')==='multicaixa' )>
                                    Multicaixa
                                </option>
                                <option value="pos" @selected(old('payment_method')==='pos' )>
                                    POS
                                </option>
                                <option value="reference" @selected(old('payment_method')==='reference' )>
                                    Referência
                                </option>
                                <option value="other" @selected(old('payment_method')==='other' )>
                                    Outro
                                </option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                Pagamentos em dinheiro exigem uma caixa aberta.
                            </p>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Referência da transacção
                            </label>
                            <input type="text" name="transaction_reference" value="{{ old('transaction_reference') }}" maxlength="100" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Observações
                            </label>
                            <textarea name="observations" rows="3" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('observations') }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 border-t pt-5">
                        <a href="{{ route('university.expenses.show', $expense) }}" class="rounded-md bg-gray-200 px-5 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300">
                            Cancelar
                        </a>

                        <button type="submit" onclick="return confirm('Confirma o registo deste pagamento?')" class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                            Registar pagamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
