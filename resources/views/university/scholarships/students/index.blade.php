<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Bolsas atribuídas a estudantes
            </h2>

            @can('scholarships.students.create')
            <a href="{{ route('university.student-scholarships.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">
                + Atribuir bolsa
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 rounded bg-green-100 p-3 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 rounded bg-red-100 p-3 text-red-800">
                {{ session('error') }}
            </div>
            @endif

            <div class="mb-6 rounded-lg bg-white p-4 shadow">
                <form method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar estudante ou bolsa..." class="rounded-md border-gray-300">

                    <select name="status" class="rounded-md border-gray-300">
                        <option value="">Todos os estados</option>
                        @foreach(['pending' => 'Pendente', 'approved' => 'Aprovada', 'rejected' => 'Rejeitada', 'suspended' => 'Suspensa', 'expired' => 'Expirada'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status')===$value)>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>

                    <div class="flex gap-2">
                        <button class="rounded-md bg-gray-800 px-4 py-2 text-white">
                            Filtrar
                        </button>
                        <a href="{{ route('university.student-scholarships.index') }}" class="rounded-md border px-4 py-2">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs uppercase">Estudante</th>
                                <th class="px-5 py-3 text-left text-xs uppercase">Bolsa</th>
                                <th class="px-5 py-3 text-left text-xs uppercase">Ano académico</th>
                                <th class="px-5 py-3 text-left text-xs uppercase">Período</th>
                                <th class="px-5 py-3 text-left text-xs uppercase">Estado</th>
                                <th class="px-5 py-3 text-right text-xs uppercase">Acções</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($scholarships as $scholarship)
                            <tr>
                                <td class="px-5 py-4 text-sm font-medium">
                                    {{ $scholarship->student->name ?? 'Estudante' }}
                                </td>

                                <td class="px-5 py-4 text-sm">
                                    {{ $scholarship->scholarshipType->name ?? '—' }}
                                </td>

                                <td class="px-5 py-4 text-sm">
                                    {{ $scholarship->academicYear->name ?? '—' }}
                                </td>

                                <td class="px-5 py-4 text-sm">
                                    {{ $scholarship->start_date?->format('d/m/Y') }}
                                    até
                                    {{ $scholarship->end_date?->format('d/m/Y') ?? 'Sem limite' }}
                                </td>

                                <td class="px-5 py-4 text-sm">
                                    @php
                                    $statusLabels = [
                                    'pending' => 'Pendente',
                                    'approved' => 'Aprovada',
                                    'rejected' => 'Rejeitada',
                                    'suspended' => 'Suspensa',
                                    'expired' => 'Expirada',
                                    ];
                                    $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'suspended' => 'bg-gray-100 text-gray-800',
                                    'expired' => 'bg-orange-100 text-orange-800',
                                    ];
                                    @endphp

                                    <span class="rounded px-2 py-1 {{ $statusColors[$scholarship->status] ?? 'bg-gray-100' }}">
                                        {{ $statusLabels[$scholarship->status] ?? ucfirst($scholarship->status) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-right text-sm">
                                    @can('scholarships.students.view')
                                    <a href="{{ route('university.student-scholarships.show', $scholarship) }}" class="text-indigo-600 hover:underline">
                                        Ver detalhes
                                    </a>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-500">
                                    Nenhum pedido de bolsa encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $scholarships->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
