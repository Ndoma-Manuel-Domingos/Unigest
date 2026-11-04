<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Integrações bancárias
                </h2>
                <p class="text-sm text-gray-500">
                    Gestão das ligações bancárias desta universidade.
                </p>
            </div>

            @can('finance.bank-integrations.create')
            <a href="{{ route('university.bank-integrations.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">
                Nova integração
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-5 p-4 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Integração
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Banco
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Conta bancária
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Ambiente
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                    Estado
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                    Acções
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse($integrations as $integration)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">
                                        {{ $integration->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $integration->public_id }}
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    {{ strtoupper($integration->provider) }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $integration->bankAccount?->name }}
                                </td>

                                <td class="px-6 py-4">
                                    {{ $integration->environment === 'sandbox' ? 'Testes' : 'Produção' }}
                                </td>

                                <td class="px-6 py-4">
                                    @if($integration->status === 'active')
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                        Activa
                                    </span>
                                    @elseif($integration->status === 'disabled')
                                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                                        Desactivada
                                    </span>
                                    @else
                                    <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                        Configurada
                                    </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('university.bank-integrations.show', $integration) }}" class="text-indigo-600 hover:underline">
                                        Consultar
                                    </a>

                                    @can('finance.bank-integrations.update')
                                    <a href="{{ route('university.bank-integrations.edit', $integration) }}" class="ml-3 text-blue-600 hover:underline">
                                        Editar
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    Ainda não existem integrações bancárias registadas.
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $integrations->links() }}
                </div>

            </div>

        </div>
    </div>

</x-app-layout>
