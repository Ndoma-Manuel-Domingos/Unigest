<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Conceitos de Taxas
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Gerir os conceitos de cobrança da universidade.
                </p>
            </div>

            @can('finance.fee-concepts.create')
            <a href="{{ route('university.fee-concepts.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <i class="fas fa-plus"></i>
                Novo conceito
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                <i class="fas fa-check-circle text-lg"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <i class="fas fa-exclamation-circle text-lg"></i>
                {{ session('error') }}
            </div>
            @endif

            <div class="rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h3 class="text-base font-semibold text-gray-800">
                        Lista de conceitos
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Consulte e administre os conceitos de taxas registados.
                    </p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Código
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Nome
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Descrição
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Recorrente
                                </th>
                                <th class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Estado
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Acções
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($concepts as $concept)
                            <tr class="transition hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="rounded-md bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-700">
                                        {{ $concept->code }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-semibold text-gray-800">
                                        {{ $concept->name }}
                                    </span>
                                </td>

                                <td class="max-w-xs px-6 py-4 text-sm text-gray-500">
                                    {{ $concept->description ?: 'Sem descrição' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    @if($concept->is_recurring)
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        Sim
                                    </span>
                                    @else
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                        Não
                                    </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    @if($concept->is_active)
                                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                        Activo
                                    </span>
                                    @else
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                                        Inactivo
                                    </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('finance.fee-concepts.update')
                                        <a href="{{ route('university.fee-concepts.edit', $concept) }}" title="Editar" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-indigo-600 transition hover:bg-indigo-50">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form method="POST" action="{{ route('university.fee-concepts.toggle-status', $concept) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" title="{{ $concept->is_active ? 'Desactivar' : 'Activar' }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-amber-600 transition hover:bg-amber-50">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </form>
                                        @endcan

                                        @can('finance.fee-concepts.delete')
                                        <form method="POST" action="{{ route('university.fee-concepts.destroy', $concept) }}" onsubmit="return confirm('Tens a certeza de que pretendes eliminar este conceito?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" title="Eliminar" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-red-600 transition hover:bg-red-50">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-file-invoice-dollar mb-3 text-4xl text-gray-300"></i>
                                        <p class="font-semibold text-gray-600">
                                            Nenhum conceito encontrado
                                        </p>
                                        <p class="mt-1 text-sm text-gray-400">
                                            Ainda não existem conceitos de taxas registados.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($concepts->hasPages())
                <div class="border-t border-gray-100 px-6 py-4">
                    {{ $concepts->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
