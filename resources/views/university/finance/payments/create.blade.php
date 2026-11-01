<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Registar Pagamento</h2>
            <p class="mt-1 text-sm text-gray-500">
                Registar um pagamento associado a uma factura.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="mb-2 font-semibold">Verifica os seguintes campos:</p>
                <ul class="list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Resumo da factura --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h3 class="font-semibold text-gray-800">Resumo da factura</h3>
                </div>

                <div class="grid grid-cols-1 gap-5 p-6 sm:grid-cols-2">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Número da factura
                        </p>
                        <p class="mt-1 font-semibold text-indigo-700">
                            {{ $invoice->invoice_number }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Estudante
                        </p>
                        <p class="mt-1 font-semibold text-gray-800">
                            {{ $invoice->student->name ?? $invoice->student->full_name ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Total da factura
                        </p>
                        <p class="mt-1 font-semibold text-gray-800">
                            {{ number_format($invoice->total, 2, ',', '.') }}
                            {{ $invoice->currency }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">
                            Valor já pago
                        </p>
                        <p class="mt-1 font-semibold text-green-600">
                            {{ number_format($invoice->amount_paid, 2, ',', '.') }}
                            {{ $invoice->currency }}
                        </p>
                    </div>

                    <div class="sm:col-span-2 rounded-lg border border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm font-medium text-amber-800">Saldo em dívida</p>
                        <p class="mt-1 text-2xl font-bold text-amber-900">
                            {{ number_format($invoice->balance, 2, ',', '.') }}
                            {{ $invoice->currency }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Formulário --}}
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h3 class="font-semibold text-gray-800">Dados do pagamento</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Preenche os dados do valor recebido.
                    </p>
                </div>

                <form method="POST" action="{{ route('university.payments.store', $invoice) }}">
                    @csrf

                    <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">

                        <div>
                            <label for="payment_date" class="mb-2 block text-sm font-semibold text-gray-700">
                                Data do pagamento
                            </label>

                            <input type="date" name="payment_date" id="payment_date" value="{{ old('payment_date', now()->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            @error('payment_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="amount" class="mb-2 block text-sm font-semibold text-gray-700">
                                Valor recebido
                            </label>

                            <div class="relative">
                                <input type="number" name="amount" id="amount" value="{{ old('amount') }}" min="0.01" max="{{ $invoice->balance }}" step="0.01" required class="w-full rounded-lg border-gray-300 pr-16 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0.00">

                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-gray-500">
                                    {{ $invoice->currency }}
                                </span>
                            </div>

                            <p class="mt-1 text-xs text-gray-500">
                                O valor não pode ultrapassar o saldo em dívida.
                            </p>

                            @error('amount')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="payment_method" class="mb-2 block text-sm font-semibold text-gray-700">
                                Método de pagamento
                            </label>

                            <select name="payment_method" id="payment_method" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Seleccionar método</option>
                                <option value="cash" @selected(old('payment_method')==='cash' )>Numerário</option>
                                <option value="bank_transfer" @selected(old('payment_method')==='bank_transfer' )>Transferência bancária</option>
                                <option value="multicaixa" @selected(old('payment_method')==='multicaixa' )>Multicaixa</option>
                                <option value="pos" @selected(old('payment_method')==='pos' )>TPA / POS</option>
                                <option value="reference" @selected(old('payment_method')==='reference' )>Referência</option>
                                <option value="other" @selected(old('payment_method')==='other' )>Outro</option>
                            </select>

                            @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="transaction_reference" class="mb-2 block text-sm font-semibold text-gray-700">
                                Referência da transacção
                            </label>

                            <input type="text" name="transaction_reference" id="transaction_reference" value="{{ old('transaction_reference') }}" maxlength="255" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Número da operação ou referência">

                            @error('transaction_reference')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label for="observations" class="mb-2 block text-sm font-semibold text-gray-700">
                                Observações
                            </label>

                            <textarea name="observations" id="observations" rows="4" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Observações adicionais sobre o pagamento...">{{ old('observations') }}</textarea>

                            @error('observations')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                        <a href="{{ route('university.invoices.show', $invoice) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            Cancelar
                        </a>

                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                            <i class="fas fa-save"></i>
                            Registar pagamento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
