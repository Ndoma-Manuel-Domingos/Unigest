<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="font-semibold text-xl text-gray-800">
                    Movimento Bancário
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $bankMovement->description }}
                </p>
            </div>

            <a href="{{ route('university.bank-movements.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm">
                Voltar
            </a>

        </div>

    </x-slot>

    <div class="py-6">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                <div class="p-6 border-b">

                    <div class="flex items-center justify-between">

                        <div>

                            @if($bankMovement->type === 'income')

                            <span class="px-3 py-1 text-sm rounded-full bg-green-100 text-green-800">
                                Entrada
                            </span>

                            @elseif($bankMovement->type === 'expense')

                            <span class="px-3 py-1 text-sm rounded-full bg-red-100 text-red-800">
                                Saída
                            </span>

                            @else

                            <span class="px-3 py-1 text-sm rounded-full bg-yellow-100 text-yellow-800">
                                Ajuste
                            </span>

                            @endif

                        </div>

                        <div class="text-2xl font-bold">

                            {{ number_format($bankMovement->amount, 2, ',', '.') }}

                            {{ $bankMovement->bankAccount->currency }}

                        </div>

                    </div>

                </div>

                <div class="p-6">

                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <dt class="text-sm text-gray-500">
                                Conta bancária
                            </dt>

                            <dd class="mt-1 font-semibold">
                                {{ $bankMovement->bankAccount->name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">
                                Banco
                            </dt>

                            <dd class="mt-1">
                                {{ $bankMovement->bankAccount->bank_name }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">
                                Data
                            </dt>

                            <dd class="mt-1">
                                {{ $bankMovement->movement_date->format('d/m/Y') }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">
                                Utilizador
                            </dt>

                            <dd class="mt-1">
                                {{ $bankMovement->user?->name ?? '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">
                                Referência
                            </dt>

                            <dd class="mt-1">
                                {{ $bankMovement->reference ?: '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">
                                Nº do documento
                            </dt>

                            <dd class="mt-1">
                                {{ $bankMovement->document_number ?: '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">
                                Referência da transacção
                            </dt>

                            <dd class="mt-1">
                                {{ $bankMovement->transaction_reference ?: '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">
                                Reconciliação
                            </dt>

                            <dd class="mt-1">

                                @if($bankMovement->reconciled)

                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    Reconciliado
                                </span>

                                @else

                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                    Pendente
                                </span>

                                @endif

                            </dd>

                        </div>

                        <div class="md:col-span-2">

                            <dt class="text-sm text-gray-500">
                                Descrição
                            </dt>

                            <dd class="mt-1">
                                {{ $bankMovement->description }}
                            </dd>

                        </div>

                        <div class="md:col-span-2">

                            <dt class="text-sm text-gray-500">
                                Observações
                            </dt>

                            <dd class="mt-1 whitespace-pre-line">
                                {{ $bankMovement->observations ?: '—' }}
                            </dd>

                        </div>

                    </dl>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
