<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800">
                Ficha do Estudante
            </h2>

            @can('students.update')
            <a href="{{ route('university.students.edit', $student) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg">
                Editar Dados
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 bg-green-100 text-green-800 p-3 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="flex flex-col md:flex-row gap-6 items-start">

                    @if($student->photo)
                    <img src="{{ Storage::disk('public')->url($student->photo) }}" class="w-36 h-36 rounded-xl object-cover">
                    @else
                    <div class="w-36 h-36 rounded-xl bg-gray-200 flex items-center justify-center text-4xl font-bold">
                        {{ strtoupper(substr($student->name, 0, 1)) }}
                    </div>
                    @endif

                    <div class="flex-1">
                        <h3 class="text-2xl font-bold">{{ $student->name }}</h3>

                        <p class="text-indigo-600 font-semibold mt-1">
                            {{ $student->student_number }}
                        </p>

                        <div class="mt-3">
                            @if($student->is_active)
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                                Estudante activo
                            </span>
                            @else
                            <span class="bg-red-100 text-red-800 px-3 py-1 rounded-full text-sm">
                                Estudante inactivo
                            </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-5">
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p>{{ $student->email ?: '—' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">Telefone</p>
                                <p>{{ $student->phone ?: '—' }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-500">NIF</p>
                                <p>{{ $student->nif ?: '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-semibold text-lg border-b pb-3 mb-4">
                        Dados Pessoais
                    </h3>

                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500">Documento</dt>
                            <dd>{{ $student->document_type ?: '—' }} — {{ $student->document_number ?: '—' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Género</dt>
                            <dd>{{ $student->gender ?: '—' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Data de Nascimento</dt>
                            <dd>{{ $student->birth_date?->format('d/m/Y') ?? '—' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Naturalidade</dt>
                            <dd>{{ $student->birth_place ?: '—' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Nacionalidade</dt>
                            <dd>{{ $student->nationality ?: '—' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Estado Civil</dt>
                            <dd>{{ $student->marital_status ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="font-semibold text-lg border-b pb-3 mb-4">
                        Residência e Encarregado
                    </h3>

                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm text-gray-500">Província</dt>
                            <dd>{{ $student->province ?: '—' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Município</dt>
                            <dd>{{ $student->municipality ?: '—' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Morada</dt>
                            <dd>{{ $student->address ?: '—' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Encarregado</dt>
                            <dd>{{ $student->guardian_name ?: '—' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Contacto do Encarregado</dt>
                            <dd>{{ $student->guardian_phone ?: '—' }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Parentesco</dt>
                            <dd>{{ $student->guardian_relationship ?: '—' }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-semibold text-lg border-b pb-3 mb-4">
                    Histórico de Matrículas
                </h3>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left">Nº Matrícula</th>
                                <th class="px-4 py-3 text-left">Ano Lectivo</th>
                                <th class="px-4 py-3 text-left">Curso</th>
                                <th class="px-4 py-3 text-left">Turma</th>
                                <th class="px-4 py-3 text-center">Ano</th>
                                <th class="px-4 py-3 text-center">Estado</th>
                                <th class="px-4 py-3 text-left">Data</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($student->enrollments as $enrollment)
                            <tr>
                                <td class="px-4 py-3">{{ $enrollment->enrollment_number }}</td>
                                <td class="px-4 py-3">{{ $enrollment->academicYear->name }}</td>
                                <td class="px-4 py-3">{{ $enrollment->course->name }}</td>
                                <td class="px-4 py-3">{{ $enrollment->academicClass->name }}</td>
                                <td class="px-4 py-3 text-center">
                                    {{ $enrollment->year_number }}º
                                </td>
                                <td class="px-4 py-3 text-center">
                                    {{ ucfirst($enrollment->status) }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ $enrollment->enrollment_date->format('d/m/Y') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-6 text-gray-500">
                                    Este estudante ainda não possui matrículas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($student->observations)
            <div class="bg-white rounded-lg shadow p-6 mt-6">
                <h3 class="font-semibold text-lg mb-3">Observações</h3>
                <p class="whitespace-pre-line">{{ $student->observations }}</p>
            </div>
            @endif

            <div class="mt-5">
                <a href="{{ route('university.students.index') }}" class="text-indigo-600 hover:underline">
                    ← Voltar à lista de estudantes
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
