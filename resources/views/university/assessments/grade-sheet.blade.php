<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Pauta da disciplina</h2>
                <p class="text-sm text-gray-500">
                    {{ $subject->name }} — {{ $academicClass->name }}
                </p>
            </div>

            <button onclick="window.print()" class="px-4 py-2 bg-gray-800 text-white rounded-md">
                Imprimir pauta
            </button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow-sm p-5">
                <div class="text-center mb-6">
                    <h2 class="text-xl font-bold">PAUTA DE CLASSIFICAÇÕES</h2>
                    <p>{{ $subject->name }}</p>
                    <p>Turma: {{ $academicClass->name }}</p>
                    <p>Ano curricular: {{ $curriculumSubject->year_number }}.º ano</p>
                    <p>Semestre: {{ $curriculumSubject->semester_number }}.º</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse border border-gray-300">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="border px-3 py-2">N.º</th>
                                <th class="border px-3 py-2 text-left">N.º estudante</th>
                                <th class="border px-3 py-2 text-left">Nome do estudante</th>
                                <th class="border px-3 py-2">Nota final</th>
                                <th class="border px-3 py-2">Resultado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $index => $item)
                            <tr>
                                <td class="border px-3 py-2 text-center">{{ $index + 1 }}</td>
                                <td class="border px-3 py-2">
                                    {{ $item->enrollment->student->student_number }}
                                </td>
                                <td class="border px-3 py-2">
                                    {{ $item->enrollment->student->name }}
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    {{ $item->final_grade ?? 'Por calcular' }}
                                </td>
                                <td class="border px-3 py-2 text-center">
                                    @if($item->status === 'approved')
                                    Aprovado
                                    @elseif($item->status === 'failed')
                                    Reprovado
                                    @elseif($item->status === 'withdrawn')
                                    Desistente
                                    @else
                                    Em avaliação
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="border px-3 py-6 text-center">
                                    Não existem estudantes inscritos nesta disciplina.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="grid grid-cols-2 gap-10 mt-16 text-center">
                    <div class="border-t border-gray-800 pt-2">
                        O Docente
                    </div>
                    <div class="border-t border-gray-800 pt-2">
                        A Direcção Académica
                    </div>
                </div>

                <p class="text-center text-sm text-gray-500 mt-10">
                    Emitido em {{ now()->format('d/m/Y H:i') }}
                </p>
            </div>
        </div>
    </div>

    <style>
        @media print {

            nav,
            header,
            button {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .shadow-sm {
                box-shadow: none !important;
            }
        }

    </style>
</x-app-layout>
