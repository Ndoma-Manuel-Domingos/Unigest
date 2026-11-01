<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">{{ $supplier->name }}</h2>
                <p class="text-sm text-gray-500">Detalhes do fornecedor</p>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('university.suppliers.index') }}" class="rounded-md border px-4 py-2 text-sm text-gray-700">
                    Voltar
                </a>

                @can('finance.suppliers.update')
                <a href="{{ route('university.suppliers.edit', $supplier) }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white">
                    Editar
                </a>
                @endcan
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

            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="mb-5 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-800">Informações do fornecedor</h3>

                    @if($supplier->is_active)
                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs text-green-700">Activo</span>
                    @else
                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs text-gray-600">Inactivo</span>
                    @endif
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    @foreach([
                    'Código' => $supplier->code,
                    'NIF' => $supplier->tax_number,
                    'E-mail' => $supplier->email,
                    'Telefone' => $supplier->phone,
                    'Cidade' => $supplier->city,
                    'Morada' => $supplier->address,
                    'Banco' => $supplier->bank_name,
                    'Conta bancária' => $supplier->bank_account,
                    'IBAN' => $supplier->iban,
                    ] as $label => $value)
                    <div>
                        <p class="text-sm text-gray-500">{{ $label }}</p>
                        <p class="mt-1 font-medium text-gray-900">{{ $value ?: '—' }}</p>
                    </div>
                    @endforeach
                </div>

                @if($supplier->notes)
                <div class="mt-6 border-t pt-5">
                    <p class="text-sm text-gray-500">Observações</p>
                    <p class="mt-1 text-gray-800">{{ $supplier->notes }}</p>
                </div>
                @endif
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="border-b px-6 py-4">
                    <h3 class="font-semibold text-gray-800">Últimas despesas deste fornecedor</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">Número</th>
                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">Descrição</th>
                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">Data</th>
                                <th class="px-6 py-3 text-right text-xs uppercase text-gray-500">Valor</th>
                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">Estado</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($supplier->expenses as $expense)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $expense->expense_number }}</td>
                                <td class="px-6 py-4 text-sm">{{ $expense->description }}</td>
                                <td class="px-6 py-4 text-sm">{{ $expense->expense_date?->format('d/m/Y') }}</td>
                                <td class="px-6 py-4 text-right text-sm">
                                    {{ number_format((float) $expense->amount, 2, ',', '.') }} {{ $expense->currency }}
                                </td>
                                <td class="px-6 py-4 text-sm">{{ ucfirst(str_replace('_', ' ', $expense->status)) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    Este fornecedor ainda não possui despesas registadas.
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
