<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Gestão de Caixa
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Abertura, consulta e controlo dos caixas da universidade.
                </p>
            </div>

            @can('finance.cash.open')
            <a href="{{ route('university.cash.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                <i class="fas fa-cash-register mr-2"></i>
                Abrir Caixa
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="p-4 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="p-4 bg-red-100 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
            @endif

            @if($openRegister)
            <div class="bg-white shadow-sm rounded-xl p-5 border-l-4 border-green-500">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <span class="text-sm font-medium text-green-700">O seu caixa está aberto</span>
                        <h3 class="text-lg font-bold text-gray-800 mt-1">
                            Caixa #{{ $openRegister->id }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Aberto em {{ $openRegister->opened_at->format('d/m/Y H:i') }}
                        </p>
                    </div>

                    <a href="{{ route('university.cash.show', $openRegister) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-center">
                        Consultar Caixa
                    </a>
                </div>
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl p-5">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <select name="status" class="w-full rounded-lg border-gray-300">
                            <option value="">Todos</option>
                            <option value="open" @selected(request('status')==='open' )>Aberto</option>
                            <option value="closed" @selected(request('status')==='closed' )>Fechado</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Operador</label>
                        <input type="text" name="operator" value="{{ request('operator') }}" class="w-full rounded-lg border-gray-300" placeholder="Nome do operador">
                    </div>

                    <div class="flex items-end gap-2">
                        <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Filtrar
                        </button>
                        <a href="{{ route('university.cash.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-xl overflow-hidden">
                <div class="p-5 border-b">
                    <h3 class="font-semibold text-gray-800">Histórico de Caixas</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Caixa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Operador</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Abertura</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecho</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Saldo inicial</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acção</th>
                            </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($registers as $register)
                            <tr>
                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    #{{ $register->id }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $register->user->name ?? 'Utilizador removido' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $register->opened_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $register->closed_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    {{ number_format($register->opening_balance, 2, ',', '.') }} AOA
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($register->status === 'open')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">Aberto</span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-700">Fechado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('university.cash.show', $register) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        Consultar
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    Ainda não existem caixas registados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $registers->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
