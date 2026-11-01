<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-bold text-gray-800">
                    Tarifas Académicas
                </h2>
                <p class="mt-1 text-sm text-gray-500">
                    Gerir os valores e a frequência das cobranças académicas.
                </p>
            </div>

            @can('finance.fee-tariffs.create')
            <a href="{{ route('university.fee-tariffs.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                <i class="fas fa-plus"></i>
                Nova tarifa
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

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h3 class="font-semibold text-gray-800">
                        Tarifas registadas
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Consulta e administra as tarifas da universidade.
                    </p>
                </div>

                {{-- Filtros --}}
                <div class="border-b border-gray-100 bg-gray-50 px-6 py-4">
                    <form method="GET" action="{{ route('university.fee-tariffs.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">

                        <div>
                            <label for="search" class="mb-1 block text-sm font-medium text-gray-700">
                                Pesquisa
                            </label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Nome ou código..." class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label for="academic_year_id" class="mb-1 block text-sm font-medium text-gray-700">
                                Ano académico
                            </label>
                            <select name="academic_year_id" id="academic_year_id" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(request('academic_year_id')==$year->id)>
                                    {{ $year->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="frequency" class="mb-1 block text-sm font-medium text-gray-700">
                                Frequência
                            </label>
                            <select name="frequency" id="frequency" class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Todas</option>
                                <option value="once" @selected(request('frequency')==='once' )>Única</option>
                                <option value="monthly" @selected(request('frequency')==='monthly' )>Mensal</option>
                                <option value="semesterly" @selected(request('frequency')==='semesterly' )>Semestral</option>
                                <option value="annually" @selected(request('frequency')==='annually' )>Anual</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button type="submit" class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                                <i class="fas fa-search"></i>
                                Filtrar
                            </button>

                            <a href="{{ route('university.fee-tariffs.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                                Limpar
                            </a>
                        </div>
                    </form>
                </div>

                {{-- Tabela --}}
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Conceito
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Ano académico
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Curso
                                </th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Frequência
                                </th>
                                <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Valor
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
                            @forelse($tariffs as $tariff)
                            <tr class="transition hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-800">
                                        {{ $tariff->feeConcept->name ?? '—' }}
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        {{ $tariff->feeConcept->code ?? '' }}
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-700">
                                    {{ $tariff->academicYear->name ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $tariff->course->name ?? 'Todos os cursos' }}
                                </td>

                                <td class="whitespace-nowrap px-6 py-4">
                                    @php
                                    $frequencies = [
                                    'once' => 'Única',
                                    'monthly' => 'Mensal',
                                    'semesterly' => 'Semestral',
                                    'annually' => 'Anual',
                                    ];
                                    @endphp

                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">
                                        {{ $frequencies[$tariff->frequency] ?? $tariff->frequency }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <span class="font-bold text-gray-800">
                                        {{ number_format($tariff->amount, 2, ',', '.') }}
                                    </span>
                                    <span class="ml-1 text-xs text-gray-500">
                                        {{ $tariff->currency }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-center">
                                    @if($tariff->is_active)
                                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                        Activa
                                    </span>
                                    @else
                                    <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                                        Inactiva
                                    </span>
                                    @endif
                                </td>

                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @can('finance.fee-tariffs.update')
                                        <a href="{{ route('university.fee-tariffs.edit', $tariff) }}" title="Editar" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-indigo-600 transition hover:bg-indigo-50">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form method="POST" action="{{ route('university.fee-tariffs.toggle-status', $tariff) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit" title="{{ $tariff->is_active ? 'Desactivar' : 'Activar' }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-gray-200 text-amber-600 transition hover:bg-amber-50">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </form>
                                        @endcan

                                        @can('finance.fee-tariffs.delete')
                                        <form method="POST" action="{{ route('university.fee-tariffs.destroy', $tariff) }}" onsubmit="return confirm('Tens a certeza de que pretendes eliminar esta tarifa?')">
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
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <i class="fas fa-money-bill-wave mb-3 text-4xl text-gray-300"></i>
                                    <p class="font-semibold text-gray-600">
                                        Nenhuma tarifa encontrada
                                    </p>
                                    <p class="mt-1 text-sm text-gray-400">
                                        Regista uma tarifa para começar.
                                    </p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tariffs->hasPages())
                <div class="border-t border-gray-100 px-6 py-4">
                    {{ $tariffs->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
