<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ $bankIntegration->name }}
                </h2>
                <p class="text-sm text-gray-500">
                    Detalhes da integração bancária.
                </p>
            </div>

            @can('finance.bank-integrations.update')
            <a href="{{ route('university.bank-integrations.edit', $bankIntegration) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">
                Editar configuração
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="p-4 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold mb-5">
                    Dados da integração
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>
                        <p class="text-sm text-gray-500">Banco</p>
                        <p class="font-medium">{{ strtoupper($bankIntegration->provider) }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Conta bancária</p>
                        <p class="font-medium">{{ $bankIntegration->bankAccount?->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Ambiente</p>
                        <p class="font-medium">{{ $bankIntegration->environment }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Estado</p>
                        <p class="font-medium">{{ $bankIntegration->status }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">URL da API</p>
                        <p class="font-medium break-all">
                            {{ $bankIntegration->base_url ?: 'Não configurada' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Última ligação</p>
                        <p class="font-medium">
                            {{ $bankIntegration->last_connection_at?->format('d/m/Y H:i') ?? 'Nunca' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Última sincronização</p>
                        <p class="font-medium">
                            {{ $bankIntegration->last_sync_at?->format('d/m/Y H:i') ?? 'Nunca' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Credenciais</p>
                        <p class="font-medium">
                            {{ $hasCredentials ? 'Registadas e protegidas' : 'Ainda não configuradas' }}
                        </p>
                    </div>

                </div>
            </div>

            @if($bankIntegration->last_error)
            <div class="p-4 bg-red-50 text-red-800 rounded-lg">
                <strong>Último erro:</strong>
                {{ $bankIntegration->last_error }}
            </div>
            @endif

            @can('finance.bank-integrations.update')
            @if($bankIntegration->status !== 'disabled')
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="font-semibold text-gray-800">
                    Desactivar integração
                </h3>

                <p class="text-sm text-gray-500 mt-1">
                    A integração deixará de poder ser utilizada para novas operações.
                </p>

                <form method="POST" action="{{ route('university.bank-integrations.disable', $bankIntegration) }}" class="mt-4" onsubmit="return confirm('Deseja realmente desactivar esta integração?')">

                    @csrf
                    @method('PATCH')

                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md">
                        Desactivar integração
                    </button>
                </form>
            </div>
            @endif
            @endcan

            <a href="{{ route('university.bank-integrations.index') }}" class="inline-block text-indigo-600 hover:underline">
                Voltar às integrações
            </a>

        </div>
    </div>

</x-app-layout>
