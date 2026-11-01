<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Avaliações</h2>
                <p class="text-sm text-gray-500">
                    Turma: {{ $academicClass->name }}
                </p>
            </div>

            @can('assessments.create')
            <a href="{{ route('university.assessments.create', $academicClass) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">
                Nova avaliação
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
                {{ session('error') }}
            </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">Avaliação</th>
                            <th class="px-4 py-3 text-left">Disciplina</th>
                            <th class="px-4 py-3 text-left">Tipo</th>
                            <th class="px-4 py-3 text-left">Data</th>
                            <th class="px-4 py-3 text-left">Ponderação</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-right">Acções</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($assessments as $assessment)
                        <tr>
                            <td class="px-4 py-3 font-medium">
                                {{ $assessment->title }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $assessment->curriculumSubject->subject->name }}
                            </td>
                            <td class="px-4 py-3">
                                {{ ucfirst(str_replace('_', ' ', $assessment->type)) }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $assessment->assessment_date->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">{{ $assessment->weight }}%</td>
                            <td class="px-4 py-3">
                                @if($assessment->is_published)
                                <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs">
                                    Publicada
                                </span>
                                @else
                                <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs">
                                    Em preparação
                                </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('university.assessments.show', $assessment) }}" class="text-indigo-600 hover:underline">
                                    Lançar notas
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                Nenhuma avaliação criada para esta turma.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">{{ $assessments->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
