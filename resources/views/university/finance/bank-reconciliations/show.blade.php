<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>
                <div class="flex items-center gap-3">

                    <h2 class="font-semibold text-xl text-gray-800">
                        {{ $bankReconciliation->reference }}
                    </h2>

                    @if($bankReconciliation->status === 'reconciled')
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                        Concluída
                    </span>
                    @else
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                        Em aberto
                    </span>
                    @endif

                </div>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $bankReconciliation->bankAccount->name }}
                    —
                    {{ $bankReconciliation->bankAccount->bank_name }}
                </p>

            </div>

            <div class="flex flex-wrap gap-2">

                <a href="{{ route('university.bank-reconciliations.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                    Voltar
                </a>

                @if($bankReconciliation->isDraft())

                @can('finance.bank-reconciliations.create')
                <a href="{{ route('university.bank-reconciliations.import', $bankReconciliation) }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                    Importar Extrato
                </a>
                @endcan

                @endif

            </div>

        </div>

    </x-slot>

    <div class="py-6">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- MENSAGENS --}}
            @if(session('success'))
            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">

                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
            @endif

            {{-- RESUMO --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <p class="text-xs font-semibold uppercase text-gray-500">
                        Saldo Inicial
                    </p>

                    <p class="mt-2 text-xl font-bold text-gray-900">
                        {{ number_format((float) $bankReconciliation->statement_opening_balance, 2, ',', '.') }}
                        {{ $bankReconciliation->bankAccount->currency }}
                    </p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <p class="text-xs font-semibold uppercase text-gray-500">
                        Saldo Extrato
                    </p>

                    <p class="mt-2 text-xl font-bold text-gray-900">
                        {{ number_format((float) $bankReconciliation->statement_closing_balance, 2, ',', '.') }}
                        {{ $bankReconciliation->bankAccount->currency }}
                    </p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
                    <p class="text-xs font-semibold uppercase text-gray-500">
                        Saldo Sistema
                    </p>

                    <p class="mt-2 text-xl font-bold text-gray-900">
                        {{ number_format((float) $bankReconciliation->system_closing_balance, 2, ',', '.') }}
                        {{ $bankReconciliation->bankAccount->currency }}
                    </p>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">

                    <p class="text-xs font-semibold uppercase text-gray-500">
                        Diferença
                    </p>

                    <p class="mt-2 text-xl font-bold
                        {{ abs((float) $bankReconciliation->difference) <= 0.01
                            ? 'text-green-600'
                            : 'text-red-600' }}">

                        {{ number_format((float) $bankReconciliation->difference, 2, ',', '.') }}
                        {{ $bankReconciliation->bankAccount->currency }}
                    </p>

                </div>

                <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">

                    <p class="text-xs font-semibold uppercase text-gray-500">
                        Linhas
                    </p>

                    <div class="mt-2 flex items-center gap-3">

                        <span class="text-xl font-bold text-gray-900">
                            {{ $matchedCount }}
                        </span>

                        <span class="text-sm text-gray-500">
                            / {{ $pendingCount }} pendentes
                        </span>

                    </div>

                </div>

            </div>

            {{-- INFORMAÇÕES --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 mb-6">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">

                    <div>
                        <p class="text-xs uppercase font-semibold text-gray-500">
                            Período
                        </p>

                        <p class="mt-1 font-medium text-gray-900">
                            {{ $bankReconciliation->period_start->format('d/m/Y') }}
                            —
                            {{ $bankReconciliation->period_end->format('d/m/Y') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase font-semibold text-gray-500">
                            Criada por
                        </p>

                        <p class="mt-1 font-medium text-gray-900">
                            {{ $bankReconciliation->creator?->name ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase font-semibold text-gray-500">
                            Linhas conciliadas
                        </p>

                        <p class="mt-1 font-medium text-green-600">
                            {{ $matchedCount }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs uppercase font-semibold text-gray-500">
                            Ignoradas
                        </p>

                        <p class="mt-1 font-medium text-gray-600">
                            {{ $ignoredCount }}
                        </p>
                    </div>

                </div>

                @if($bankReconciliation->notes)

                <div class="mt-5 pt-5 border-t border-gray-200">

                    <p class="text-xs uppercase font-semibold text-gray-500">
                        Observações
                    </p>

                    <p class="mt-1 text-sm text-gray-700 whitespace-pre-line">
                        {{ $bankReconciliation->notes }}
                    </p>

                </div>

                @endif

            </div>

            {{-- ALERTA --}}
            @if($bankReconciliation->isDraft())

            @if($pendingCount > 0)

            <div class="mb-6 rounded-lg bg-yellow-50 border border-yellow-200 p-4">

                <p class="font-semibold text-yellow-800">
                    Existem {{ $pendingCount }} linhas pendentes.
                </p>

                <p class="text-sm text-yellow-700 mt-1">
                    Concilie ou ignore todas as linhas antes de concluir.
                </p>

            </div>

            @elseif(abs((float) $bankReconciliation->difference) > 0.01)

            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">

                <p class="font-semibold text-red-800">
                    A reconciliação apresenta uma diferença.
                </p>

                <p class="text-sm text-red-700 mt-1">
                    A diferença deve ser zero antes da conclusão.
                </p>

            </div>

            @else

            <div class="mb-6 rounded-lg bg-green-50 border border-green-200 p-4">

                <p class="font-semibold text-green-800">
                    Reconciliação pronta para conclusão.
                </p>

            </div>

            @endif

            @endif

            {{-- LINHAS DO EXTRATO --}}
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">

                <div class="p-5 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    <div>
                        <h3 class="font-semibold text-gray-800">
                            Linhas do Extrato
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Compare cada movimento do banco com o sistema.
                        </p>
                    </div>

                    @if($bankReconciliation->isDraft() && $pendingCount === 0 && abs((float) $bankReconciliation->difference) <= 0.01) @can('finance.bank-reconciliations.reconcile') <form method="POST" action="{{ route('university.bank-reconciliations.reconcile', $bankReconciliation) }}">

                        @csrf

                        <button type="submit" onclick="return confirm('Tem certeza que deseja concluir esta reconciliação?')" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                            Concluir Reconciliação
                        </button>

                        </form>

                        @endcan

                        @endif

                </div>

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">

                            <tr>

                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Data
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Descrição
                                </th>

                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                    Referência
                                </th>

                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Tipo
                                </th>

                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Valor
                                </th>

                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">
                                    Estado
                                </th>

                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">
                                    Ação
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse($lines as $line)

                            <tr class="hover:bg-gray-50">

                                <td class="px-5 py-4 whitespace-nowrap text-sm">
                                    {{ $line->transaction_date->format('d/m/Y') }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="font-medium text-gray-900">
                                        {{ $line->description }}
                                    </div>

                                    @if($line->observations)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $line->observations }}
                                    </div>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-sm text-gray-600">
                                    {{ $line->reference ?: '—' }}
                                </td>

                                <td class="px-5 py-4 text-center">

                                    @if($line->type === 'income')

                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Entrada
                                    </span>

                                    @else

                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        Saída
                                    </span>

                                    @endif

                                </td>

                                <td class="px-5 py-4 text-right font-semibold">
                                    {{ number_format((float) $line->amount, 2, ',', '.') }}
                                    {{ $bankReconciliation->bankAccount->currency }}
                                </td>

                                <td class="px-5 py-4 text-center">

                                    @if($line->status === 'matched')

                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                        Conciliado
                                    </span>

                                    @elseif($line->status === 'ignored')

                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">
                                        Ignorado
                                    </span>

                                    @else

                                    <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                        Pendente
                                    </span>

                                    @endif

                                </td>

                                <td class="px-5 py-4 text-right">

                                    @if($line->status === 'matched')

                                    @if($line->matchedBankMovement)

                                    <div class="text-xs text-gray-500 mb-2">
                                        Movimento #{{ $line->matchedBankMovement->id }}
                                    </div>

                                    @endif

                                    @can('finance.bank-reconciliations.match')

                                    @if($bankReconciliation->isDraft())

                                    <form method="POST" action="{{ route('university.bank-reconciliations.unmatch', [$bankReconciliation, $line]) }}">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                            Desfazer
                                        </button>

                                    </form>

                                    @endif

                                    @endcan

                                    @elseif($line->status === 'pending' && $bankReconciliation->isDraft())

                                    @can('finance.bank-reconciliations.match')

                                    <form method="POST" action="{{ route('university.bank-reconciliations.match', [$bankReconciliation, $line]) }}" class="flex items-center justify-end gap-2">

                                        @csrf

                                        <select name="bank_movement_id" required class="text-sm rounded-lg border-gray-300 max-w-xs">

                                            <option value="">
                                                Selecionar movimento
                                            </option>

                                            @foreach($movements as $movement)

                                            @if(!$movement->reconciled)

                                            @php
                                            $compatible =
                                            ($line->type === 'income' && $movement->isIncome())
                                            ||
                                            ($line->type === 'expense' && $movement->isExpense());

                                            $sameAmount =
                                            abs((float) $line->amount - (float) $movement->amount) <= 0.01; @endphp @if($compatible && $sameAmount) <option value="{{ $movement->id }}">
                                                #{{ $movement->id }}
                                                —
                                                {{ $movement->movement_date->format('d/m/Y') }}
                                                —
                                                {{ $movement->description }}
                                                —
                                                {{ number_format((float) $movement->amount, 2, ',', '.') }}
                                                </option>

                                                @endif

                                                @endif

                                                @endforeach

                                        </select>

                                        <button type="submit" class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-sm hover:bg-indigo-700">
                                            Conciliar
                                        </button>

                                    </form>

                                    @endcan

                                    @can('finance.bank-reconciliations.match')

                                    <form method="POST" action="{{ route('university.bank-reconciliations.ignore', [$bankReconciliation, $line]) }}" class="mt-2">

                                        @csrf

                                        <button type="submit" class="text-sm text-gray-600 hover:text-gray-900">
                                            Ignorar
                                        </button>

                                    </form>

                                    @endcan

                                    @endif

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="7" class="px-6 py-12 text-center">

                                    <p class="text-gray-500">
                                        Nenhuma linha de extrato foi importada.
                                    </p>

                                    @if($bankReconciliation->isDraft())

                                    @can('finance.bank-reconciliations.create')

                                    <a href="{{ route('university.bank-reconciliations.import', $bankReconciliation) }}" class="inline-flex mt-3 px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                                        Importar Extrato
                                    </a>

                                    @endcan

                                    @endif

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if($lines->hasPages())

                <div class="p-4 border-t border-gray-200">
                    {{ $lines->links() }}
                </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>
