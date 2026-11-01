<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ $expense->expense_number }}
                </h2>
                <p class="text-sm text-gray-500">{{ $expense->description }}</p>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('university.expenses.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700">
                    Voltar
                </a>

                @if($expense->status === 'draft')
                @can('finance.expenses.update')
                <a href="{{ route('university.expenses.edit', $expense) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white">
                    Editar
                </a>
                @endcan
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="rounded-md bg-green-50 p-4 text-green-700">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="rounded-md bg-red-50 p-4 text-red-700">
                <ul class="list-inside list-disc">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="rounded-lg bg-white p-6 shadow-sm lg:col-span-2">
                    <h3 class="font-semibold text-gray-800">Informações da despesa</h3>

                    <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <p class="text-sm text-gray-500">Fornecedor</p>
                            <p class="mt-1 font-medium">{{ $expense->supplier?->name ?? 'Não indicado' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Categoria</p>
                            <p class="mt-1 font-medium">{{ $expense->category?->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Referência</p>
                            <p class="mt-1 font-medium">{{ $expense->reference ?: '—' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Data da despesa</p>
                            <p class="mt-1 font-medium">{{ $expense->expense_date?->format('d/m/Y') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Data limite</p>
                            <p class="mt-1 font-medium">{{ $expense->due_date?->format('d/m/Y') ?? '—' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Registada por</p>
                            <p class="mt-1 font-medium">{{ $expense->creator?->name ?? '—' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Aprovada por</p>
                            <p class="mt-1 font-medium">{{ $expense->approver?->name ?? '—' }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Data de aprovação</p>
                            <p class="mt-1 font-medium">{{ $expense->approved_at?->format('d/m/Y H:i') ?? '—' }}</p>
                        </div>

                        @if($expense->notes)
                        <div class="sm:col-span-2">
                            <p class="text-sm text-gray-500">Observações</p>
                            <p class="mt-1 whitespace-pre-line">{{ $expense->notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <h3 class="font-semibold text-gray-800">Resumo financeiro</h3>

                    <div class="mt-5 space-y-4">
                        <div>
                            <p class="text-sm text-gray-500">Valor total</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ number_format((float) $expense->amount, 2, ',', '.') }}
                                <span class="text-sm font-medium">{{ $expense->currency }}</span>
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Valor pago</p>
                            <p class="text-lg font-semibold text-green-700">
                                {{ number_format((float) $expense->paid_amount, 2, ',', '.') }}
                                {{ $expense->currency }}
                            </p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Saldo em dívida</p>
                            <p class="text-lg font-semibold text-red-700">
                                {{ number_format((float) $expense->balance, 2, ',', '.') }}
                                {{ $expense->currency }}
                            </p>
                        </div>

                        <div class="border-t pt-4">
                            <p class="text-sm text-gray-500">Estado</p>
                            <p class="mt-1 font-semibold">
                                {{ ucfirst(str_replace('_', ' ', $expense->status)) }}
                            </p>
                        </div>

                        @if($expense->paid_at)
                        <div>
                            <p class="text-sm text-gray-500">Último pagamento</p>
                            <p class="font-medium">{{ $expense->paid_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <h3 class="font-semibold text-gray-800">Operações</h3>

                <div class="mt-4 flex flex-wrap gap-3">



                    @if($expense->canBeApproved())
                    @can('finance.expenses.approve')
                    <form method="POST" action="{{ route('university.expenses.approve', $expense) }}" onsubmit="return confirm('Confirma a aprovação desta despesa?')">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-700">
                            Aprovar despesa
                        </button>
                    </form>
                    @endcan
                    @endif

                    @if($expense->canBeCancelled())
                    @can('finance.expenses.cancel')
                    <form method="POST" action="{{ route('university.expenses.cancel', $expense) }}" onsubmit="return confirm('Tem a certeza de que deseja cancelar esta despesa?')">
                        @csrf
                        @method('PATCH')

                        <button type="submit" class="rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">
                            Cancelar despesa
                        </button>
                    </form>
                    @endcan
                    @endif

                    @if(!$expense->canBeApproved() && !$expense->canBeCancelled())
                    <p class="text-sm text-gray-500">
                        Não existem operações disponíveis para esta despesa neste estado.
                    </p>
                    @endif

                </div>
            </div>

            <div class="mt-6 rounded-lg bg-white p-6 shadow">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">
                            Histórico de pagamentos
                        </h3>
                        <p class="text-sm text-gray-500">
                            Pagamentos efectuados para esta despesa.
                        </p>
                    </div>

                    @if (in_array($expense->status, ['approved', 'partially_paid']) && (float) $expense->balance > 0)
                    @can('finance.expenses.payments.create')
                    <a href="{{ route('university.expenses.payments.create', $expense) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
                        Registar pagamento
                    </a>
                    @endcan
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                    Nº pagamento
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                    Data
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                    Método
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">
                                    Utilizador
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase text-gray-500">
                                    Valor
                                </th>
                                <th class="px-4 py-3 text-center text-xs font-medium uppercase text-gray-500">
                                    Estado
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($expense->payments as $payment)
                            <tr>
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $payment->payment_number }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                    {{ $payment->payment_date?->format('d/m/Y H:i') }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                    {{ [
                                'cash' => 'Dinheiro',
                                'bank_transfer' => 'Transferência',
                                'multicaixa' => 'Multicaixa',
                                'pos' => 'POS',
                                'reference' => 'Referência',
                                'other' => 'Outro',
                            ][$payment->payment_method] ?? $payment->payment_method }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                    {{ $payment->user?->name ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-right text-sm font-semibold text-gray-900">
                                    {{ number_format($payment->amount, 2, ',', '.') }}
                                    {{ $payment->currency }}
                                </td>

                                <td class="whitespace-nowrap px-4 py-3 text-center">
                                    <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-700">
                                        {{ $payment->status === 'confirmed' ? 'Confirmado' : $payment->status }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500">
                                    Ainda não existem pagamentos registados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
