<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>
                <h2 class="font-semibold text-xl text-gray-800">
                    {{ $bankAccount->name }}
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $bankAccount->bank_name }}
                </p>
            </div>

            <div class="flex gap-2">

                @can('finance.bank-accounts.update')
                <a href="{{ route('university.bank-accounts.edit', $bankAccount) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm hover:bg-indigo-700">
                    Editar
                </a>
                @endcan

                <a href="{{ route('university.bank-accounts.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md text-sm hover:bg-gray-300">
                    Voltar
                </a>

            </div>

        </div>

    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">
                        Saldo actual
                    </div>

                    <div class="text-3xl font-bold text-indigo-600 mt-2">
                        {{ number_format($bankAccount->current_balance, 2, ',', '.') }}
                        {{ $bankAccount->currency }}
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">
                        Saldo inicial
                    </div>

                    <div class="text-2xl font-bold text-gray-800 mt-2">
                        {{ number_format($bankAccount->opening_balance, 2, ',', '.') }}
                        {{ $bankAccount->currency }}
                    </div>
                </div>

                <div class="bg-white shadow-sm rounded-lg p-6">
                    <div class="text-sm text-gray-500">
                        Movimentos
                    </div>

                    <div class="text-2xl font-bold text-gray-800 mt-2">
                        {{-- {{ $bankAccount->movements_count ?? $bankAccount->movements()->count() }} --}}
                        {{ $movementsCount }}
                    </div>
                </div>

            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-1">

                    <div class="bg-white shadow-sm rounded-lg p-6">

                        <h3 class="font-semibold text-gray-800 mb-5">
                            Dados da conta
                        </h3>

                        <dl class="space-y-4">

                            <div>
                                <dt class="text-sm text-gray-500">Banco</dt>
                                <dd class="font-medium text-gray-900">
                                    {{ $bankAccount->bank_name }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm text-gray-500">Número da conta</dt>
                                <dd class="font-medium text-gray-900">
                                    {{ $bankAccount->account_number ?: '—' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm text-gray-500">IBAN</dt>
                                <dd class="font-medium text-gray-900">
                                    {{ $bankAccount->iban ?: '—' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm text-gray-500">SWIFT</dt>
                                <dd class="font-medium text-gray-900">
                                    {{ $bankAccount->swift ?: '—' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm text-gray-500">Agência / Balcão</dt>
                                <dd class="font-medium text-gray-900">
                                    {{ $bankAccount->branch ?: '—' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="text-sm text-gray-500">Estado</dt>
                                <dd class="mt-1">

                                    @if($bankAccount->is_active)
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        Activa
                                    </span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">
                                        Inactiva
                                    </span>
                                    @endif

                                </dd>
                            </div>

                            @if($bankAccount->is_default)
                            <div>
                                <span class="px-2 py-1 text-xs rounded-full bg-indigo-100 text-indigo-800">
                                    Conta principal
                                </span>
                            </div>
                            @endif

                        </dl>

                    </div>

                </div>

                <div class="lg:col-span-2">

                    <div class="bg-white shadow-sm rounded-lg">

                        <div class="p-6 border-b">
                            <h3 class="font-semibold text-gray-800">
                                Últimos movimentos
                            </h3>
                        </div>

                        <div class="overflow-x-auto">

                            <table class="min-w-full divide-y divide-gray-200">

                                <thead class="bg-gray-50">

                                    <tr>

                                        <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                            Data
                                        </th>

                                        <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                            Descrição
                                        </th>

                                        <th class="px-6 py-3 text-center text-xs uppercase text-gray-500">
                                            Tipo
                                        </th>

                                        <th class="px-6 py-3 text-right text-xs uppercase text-gray-500">
                                            Valor
                                        </th>

                                    </tr>

                                </thead>

                                <tbody class="divide-y divide-gray-200">

                                    @forelse($bankAccount->movements as $movement)

                                    <tr>

                                        <td class="px-6 py-4 text-sm">
                                            {{ $movement->movement_date?->format('d/m/Y') }}
                                        </td>

                                        <td class="px-6 py-4 text-sm">
                                            {{ $movement->description }}
                                        </td>

                                        <td class="px-6 py-4 text-center">

                                            @if($movement->isIncome())
                                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                Entrada
                                            </span>
                                            @else
                                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                                Saída
                                            </span>
                                            @endif

                                        </td>

                                        <td class="px-6 py-4 text-right font-medium">
                                            {{ number_format($movement->amount, 2, ',', '.') }}
                                            {{ $bankAccount->currency }}
                                        </td>

                                    </tr>

                                    @empty

                                    <tr>
                                        <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                                            Nenhum movimento bancário registado.
                                        </td>
                                    </tr>

                                    @endforelse

                                </tbody>

                            </table>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>
