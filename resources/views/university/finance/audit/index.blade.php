<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Auditoria Financeira
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <form method="GET" class="bg-white p-5 rounded-xl shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Módulo</label>
                        <select name="module" class="w-full rounded-lg border-gray-300">
                            <option value="">Todos</option>
                            <option value="banking" @selected(request('module')==='banking' )>
                                Bancário
                            </option>
                            <option value="invoices" @selected(request('module')==='invoices' )>
                                Facturação
                            </option>
                            <option value="payments" @selected(request('module')==='payments' )>
                                Pagamentos
                            </option>
                            <option value="expenses" @selected(request('module')==='expenses' )>
                                Despesas
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Evento</label>
                        <input type="text" name="event" value="{{ request('event') }}" placeholder="Ex.: created, confirmed" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Data inicial</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-lg border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Data final</label>
                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-lg border-gray-300">
                    </div>
                </div>

                <div class="flex gap-2 mt-4">
                    <button class="px-5 py-2 bg-indigo-600 text-white rounded-lg">
                        Filtrar
                    </button>

                    <a href="{{ route('university.financial-audit.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">
                        Limpar
                    </a>
                </div>
            </form>

            <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold">Data</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold">Utilizador</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold">Módulo</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold">Evento</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold">Descrição</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold">Acção</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse($logs as $log)
                            <tr>
                                <td class="px-5 py-3 text-sm whitespace-nowrap">
                                    {{ $log->created_at?->format('d/m/Y H:i:s') }}
                                </td>

                                <td class="px-5 py-3 text-sm">
                                    {{ $log->user?->name ?? 'Utilizador removido' }}
                                </td>

                                <td class="px-5 py-3 text-sm">
                                    {{ ucfirst($log->module) }}
                                </td>

                                <td class="px-5 py-3 text-sm">
                                    {{ $log->event }}
                                </td>

                                <td class="px-5 py-3 text-sm">
                                    {{ $log->description }}
                                </td>

                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('university.financial-audit.show', $log->id) }}" class="text-indigo-600 hover:text-indigo-800">
                                        Detalhes
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                    Nenhum registo de auditoria encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
