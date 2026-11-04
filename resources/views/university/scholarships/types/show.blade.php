<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Detalhes da bolsa
            </h2>

            <a href="{{ route('university.scholarship-types.index') }}" class="text-sm text-indigo-600">
                Voltar à listagem
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="text-lg font-semibold">
                    {{ $scholarshipType->name }}
                </h3>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-500">Código</p>
                        <p class="font-medium">{{ $scholarshipType->code }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Tipo</p>
                        <p class="font-medium">
                            {{ $scholarshipType->type === 'percentage' ? 'Percentagem' : 'Valor fixo' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Valor</p>
                        <p class="font-medium">
                            {{ number_format($scholarshipType->value, 2, ',', '.') }}
                            {{ $scholarshipType->type === 'percentage' ? '%' : 'AOA' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Desconto máximo</p>
                        <p class="font-medium">
                            {{ $scholarshipType->maximum_amount !== null
                                ? number_format($scholarshipType->maximum_amount, 2, ',', '.') . ' AOA'
                                : 'Sem limite definido' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Estado</p>
                        <p class="font-medium">
                            {{ $scholarshipType->is_active ? 'Ativa' : 'Inativa' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Total de atribuições</p>
                        <p class="font-medium">
                            {{ $scholarshipType->studentScholarships->count() }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Descrição</p>
                        <p class="font-medium">
                            {{ $scholarshipType->description ?: 'Sem descrição.' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b p-5">
                    <h3 class="font-semibold">Estudantes associados</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs uppercase">Estudante</th>
                                <th class="px-5 py-3 text-left text-xs uppercase">Ano académico</th>
                                <th class="px-5 py-3 text-left text-xs uppercase">Início</th>
                                <th class="px-5 py-3 text-left text-xs uppercase">Fim</th>
                                <th class="px-5 py-3 text-left text-xs uppercase">Estado</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse($scholarshipType->studentScholarships as $assignment)
                            <tr>
                                <td class="px-5 py-3 text-sm">
                                    {{ $assignment->student->name ?? 'Estudante' }}
                                </td>
                                <td class="px-5 py-3 text-sm">
                                    {{ $assignment->academicYear->name ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-sm">
                                    {{ $assignment->start_date?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-5 py-3 text-sm">
                                    {{ $assignment->end_date?->format('d/m/Y') ?? 'Sem limite' }}
                                </td>
                                <td class="px-5 py-3 text-sm">
                                    {{ ucfirst($assignment->status) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-5 py-6 text-center text-gray-500">
                                    Ainda não existem estudantes associados.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
