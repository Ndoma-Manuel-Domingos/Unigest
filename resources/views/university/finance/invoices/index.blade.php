<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Facturação</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Gerir facturas e acompanhar os valores em cobrança.
                </p>
            </div>

            @can('finance.invoices.create')
            <a href="{{ route('university.invoices.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <i class="fas fa-plus"></i>
                Nova factura
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h3 class="font-semibold text-gray-800">Facturas registadas</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Consulta, pesquisa e acompanha as facturas emitidas.
                    </p>
                </div>

                <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                    <form method="GET" action="{{ route('university.invoices.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">

                        <div class="md:col-span-2">
                            <label for="search" class="mb-1 block text-sm font-medium text-gray-700">
                                Pesquisa
                            </label>
                            <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="Número da factura ou estudante..." class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="status" class="mb-1 block text-sm font-medium text-gray-700">
                                Estado
                            </label>
                            <select name="status" id="status" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos os estados</option>
                                <option value="draft" @selected(request('status')==='draft' )>Rascunho</option>
                                <option value="issued" @selected(request('status')==='issued' )>Emitida</option>
                                <option value="partially_paid" @selected(request('status')==='partially_paid' )>Parcialmente paga</option>
                                <option value="paid" @selected(request('status')==='paid' )>Paga</option>
                                <option value="overdue" @selected(request('status')==='overdue' )>Vencida</option>
                                <option value="cancelled" @selected(request('status')==='cancelled' )>Anulada</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                                <i class="fas fa-search"></i>
                                Filtrar
                            </button>

                            <a href="{{ route('university.invoices.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Factura</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Estudante</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Emissão</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Vencimento</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Total</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Pago</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Saldo</th>
                                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">Estado</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Acções</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($invoices as $invoice)
                            @php
                            $statuses = [
                            'draft' => ['Rascunho', 'bg-gray-100 text-gray-700'],
                            'issued' => ['Emitida', 'bg-blue-50 text-blue-700'],
                            'partially_paid' => ['Parcialmente paga', 'bg-amber-50 text-amber-700'],
                            'paid' => ['Paga', 'bg-green-50 text-green-700'],
                            'overdue' => ['Vencida', 'bg-red-50 text-red-700'],
                            'cancelled' => ['Anulada', 'bg-gray-200 text-gray-600'],
                            ];

                            $status = $statuses[$invoice->status] ?? [$invoice->status, 'bg-gray-100 text-gray-700'];
                            @endphp

                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="font-semibold text-indigo-700">
                                        {{ $invoice->invoice_number }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-800">
                                        {{ $invoice->student->name ?? $invoice->student->full_name ?? '—' }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $invoice->student->student_number ?? '' }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ $invoice->created_at?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-600">
                                    {{ $invoice->due_date?->format('d/m/Y') ?? '—' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-gray-800">
                                    {{ number_format($invoice->total, 2, ',', '.') }}
                                    <small>{{ $invoice->currency }}</small>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm text-green-700">
                                    {{ number_format($invoice->amount_paid, 2, ',', '.') }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-gray-800">
                                    {{ number_format($invoice->balance, 2, ',', '.') }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $status[1] }}">
                                        {{ $status[0] }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <a href="{{ route('university.invoices.show', $invoice) }}" title="Consultar factura" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-indigo-600 transition hover:bg-indigo-50">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center">
                                    <i class="fas fa-file-invoice mb-3 text-4xl text-gray-300"></i>
                                    <p class="font-semibold text-gray-600">Nenhuma factura encontrada</p>
                                    <p class="mt-1 text-sm text-gray-400">
                                        Ainda não existem facturas para apresentar.
                                    </p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($invoices->hasPages())
                <div class="border-t border-gray-100 px-6 py-4">
                    {{ $invoices->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
