<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="text-xl font-semibold text-gray-800">
                Detalhes da Auditoria
            </h2>

            <a href="{{ route('university.financial-audit.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-5">

            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4">
                    Informação da operação
                </h3>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <dt class="text-sm text-gray-500">Data</dt>
                        <dd class="font-medium">
                            {{ $log->created_at?->format('d/m/Y H:i:s') }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Utilizador</dt>
                        <dd class="font-medium">
                            {{ $log->user?->name ?? 'Utilizador removido' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Módulo</dt>
                        <dd class="font-medium">{{ $log->module }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Evento</dt>
                        <dd class="font-medium">{{ $log->event }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Registo afectado</dt>
                        <dd class="font-medium break-all">
                            {{ $log->auditable_type ?? 'Não aplicável' }}
                            #{{ $log->auditable_id ?? '-' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="text-sm text-gray-500">Endereço IP</dt>
                        <dd class="font-medium">{{ $log->ip_address ?? '-' }}</dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-sm text-gray-500">Descrição</dt>
                        <dd class="font-medium">{{ $log->description }}</dd>
                    </div>

                    <div class="md:col-span-2">
                        <dt class="text-sm text-gray-500">Navegador / dispositivo</dt>
                        <dd class="text-sm break-all">{{ $log->user_agent ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold mb-3">Valores anteriores</h3>
                    <pre class="text-xs bg-gray-900 text-green-300 p-4 rounded-lg overflow-x-auto">{{ json_encode($log->old_values ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="font-semibold mb-3">Novos valores</h3>
                    <pre class="text-xs bg-gray-900 text-green-300 p-4 rounded-lg overflow-x-auto">{{ json_encode($log->new_values ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>

            @if($log->metadata)
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="font-semibold mb-3">Informação adicional</h3>
                <pre class="text-xs bg-gray-900 text-green-300 p-4 rounded-lg overflow-x-auto">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif

        </div>
    </div>
</x-app-layout>
