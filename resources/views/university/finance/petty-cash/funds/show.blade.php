<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">{{ $pettyCashFund->name }}</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Fundo {{ $pettyCashFund->code }} ·
                    Responsável: {{ $pettyCashFund->responsible?->name ?? '—' }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">

                @can('finance.petty-cash.replenishments.create')
                @if($pettyCashFund->is_active &&
                (int) $pettyCashFund->responsible_user_id === (int) auth()->id())
                <a href="{{ route('university.petty-cash.replenishments.create', $pettyCashFund) }}" class="px-4 py-2 rounded-lg bg-green-600 text-white text-sm">
                    Solicitar reposição
                </a>
                @endif
                @endcan

                @can('finance.petty-cash.replenishments.view')
                <a href="{{ route('university.petty-cash.replenishments.index', ['fund_id' => $pettyCashFund->id]) }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">
                    Ver reposições
                </a>
                @endcan

                @can('finance.petty-cash.movements.create')
                <a href="{{ route('university.petty-cash.movements.create', $pettyCashFund) }}" class="px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm">
                    Registar despesa
                </a>
                @endcan

                @can('finance.petty-cash.funds.update')
                <a href="{{ route('university.petty-cash.funds.edit', $pettyCashFund) }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm">
                    Editar fundo
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="p-4 rounded-lg bg-green-50 text-green-800">{{ session('success') }}</div>
            @endif

            @if($errors->any())
            <div class="p-4 rounded-lg bg-red-50 text-red-700">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Valor autorizado</p>
                    <p class="text-2xl font-bold mt-2">
                        {{ number_format((float) $pettyCashFund->authorized_amount, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $pettyCashFund->currency }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Saldo disponível</p>
                    <p class="text-2xl font-bold mt-2 text-green-700">
                        {{ number_format((float) $pettyCashFund->current_balance, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $pettyCashFund->currency }}</p>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Limite ainda não utilizado</p>
                    <p class="text-2xl font-bold mt-2 text-indigo-700">
                        {{ number_format($pettyCashFund->remaining_limit, 2, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-400">{{ $pettyCashFund->currency }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-5">
                <div class="flex justify-between items-start gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-800">Informações do fundo</h3>
                        <p class="text-sm text-gray-500 mt-1">
                            Departamento: {{ $pettyCashFund->department?->name ?? 'Não associado' }}
                        </p>
                        <p class="text-sm text-gray-500">
                            Estado: {{ $pettyCashFund->is_active ? 'Activo' : 'Inactivo' }}
                        </p>
                    </div>

                    @can('finance.petty-cash.funds.update')
                    <form method="POST" action="{{ route('university.petty-cash.funds.toggle-status', $pettyCashFund) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" onclick="return confirm('Confirma a alteração do estado deste fundo?')" class="px-3 py-2 rounded-lg border text-sm text-gray-700 hover:bg-gray-50">
                            {{ $pettyCashFund->is_active ? 'Desactivar fundo' : 'Activar fundo' }}
                        </button>
                    </form>
                    @endcan
                </div>

                @if($pettyCashFund->description)
                <p class="mt-4 text-sm text-gray-700">{{ $pettyCashFund->description }}</p>
                @endif
            </div>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-gray-800">Histórico de movimentos</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Referência</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Data</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Descrição</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipo</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Valor</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Estado</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Acção</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($movements as $movement)
                            <tr>
                                <td class="px-5 py-4 text-sm font-medium">{{ $movement->movement_number }}</td>
                                <td class="px-5 py-4 text-sm">{{ $movement->movement_date?->format('d/m/Y') }}</td>
                                <td class="px-5 py-4 text-sm">
                                    {{ $movement->description }}
                                    <p class="text-xs text-gray-500">{{ $movement->supplier_name }}</p>
                                </td>
                                <td class="px-5 py-4 text-sm">
                                    {{ ['expense' => 'Despesa', 'replenishment' => 'Reposição', 'adjustment' => 'Abertura/Ajuste'][$movement->type] ?? $movement->type }}
                                </td>
                                <td class="px-5 py-4 text-right text-sm font-medium">
                                    {{ number_format((float) $movement->amount, 2, ',', '.') }}
                                    {{ $pettyCashFund->currency }}
                                </td>
                                <td class="px-5 py-4 text-center text-xs">
                                    {{ ['pending' => 'Pendente', 'approved' => 'Aprovado', 'rejected' => 'Rejeitado', 'cancelled' => 'Cancelado'][$movement->status] ?? $movement->status }}
                                </td>
                                <td class="px-5 py-4 text-right">
                                    @if($movement->status === 'pending' && $movement->type === 'expense')
                                    @can('finance.petty-cash.movements.approve')
                                    @if((int) $movement->user_id !== (int) auth()->id())
                                    <div class="flex justify-end gap-2">
                                        <form method="POST" action="{{ route('university.petty-cash.movements.approve', $movement) }}">
                                            @csrf
                                            <button class="text-green-700 text-sm font-medium">Aprovar</button>
                                        </form>
                                        <form method="POST" action="{{ route('university.petty-cash.movements.reject', $movement) }}">
                                            @csrf
                                            <input type="hidden" name="approval_notes" value="Rejeitado pela administração.">
                                            <button class="text-red-700 text-sm font-medium">Rejeitar</button>
                                        </form>
                                    </div>
                                    @endif
                                    @endcan
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-gray-500">
                                    Ainda não existem movimentos.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-5 border-t">{{ $movements->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
