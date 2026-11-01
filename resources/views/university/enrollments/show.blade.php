<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">
                Matrícula {{ $enrollment->enrollment_number }}
            </h2>

            @can('subject_enrollments.view')
            <a href="{{ route('university.subject-enrollments.index', $enrollment) }}" class="inline-block mt-5 px-4 py-2 bg-indigo-600 text-white rounded-md">
                Gerir disciplinas inscritas
            </a>
            @endcan

            @can('assessments.view')
            <a href="{{ route('university.assessments.index', $enrollment->academic_class_id) }}" class="inline-block mt-5 px-4 py-2 bg-blue-700 text-white rounded-md">
                Avaliações da turma
            </a>
            @endcan

            <a href="{{ route('university.enrollments.index') }}" class="px-4 py-2 border rounded-md">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-5">

            @if(session('success'))
            <div class="p-3 bg-green-100 text-green-800 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="p-3 bg-red-100 text-red-800 rounded">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-lg p-6">
                <div class="flex flex-wrap justify-between items-center gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Número de matrícula</p>
                        <h3 class="text-2xl font-bold">
                            {{ $enrollment->enrollment_number }}
                        </h3>
                    </div>

                    <a href="{{ route('university.enrollments.print', $enrollment) }}" target="_blank" class="px-4 py-2 bg-gray-800 text-white rounded-md">
                        Imprimir ficha
                    </a>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-lg mb-4">Dados do estudante</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Nome completo</p>
                        <p class="font-medium">{{ $enrollment->student->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Número de estudante</p>
                        <p>{{ $enrollment->student->student_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">NIF</p>
                        <p>{{ $enrollment->student->nif ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">E-mail</p>
                        <p>{{ $enrollment->student->email ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Telefone</p>
                        <p>{{ $enrollment->student->phone ?: '—' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-lg mb-4">Dados académicos</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Ano académico</p>
                        <p>{{ $enrollment->academicYear->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Curso</p>
                        <p>{{ $enrollment->course->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Turma</p>
                        <p>{{ $enrollment->academicClass->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Plano curricular</p>
                        <p>{{ $enrollment->curriculum->name ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Ano curricular</p>
                        <p>{{ $enrollment->year_number }}.º ano</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Semestre</p>
                        <p>
                            {{ $enrollment->semester_number
                                ? $enrollment->semester_number . '.º semestre'
                                : '—' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Data da matrícula</p>
                        <p>{{ $enrollment->enrollment_date?->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Tipo</p>
                        <p>{{ ucfirst($enrollment->enrollment_type) }}</p>
                    </div>
                </div>

                @if($enrollment->observations)
                <div class="mt-5 border-t pt-4">
                    <p class="text-sm text-gray-500">Observações</p>
                    <p>{{ $enrollment->observations }}</p>
                </div>
                @endif
            </div>

            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="font-semibold text-lg mb-4">Estado da matrícula</h3>

                <p class="mb-4">
                    Estado actual:
                    <strong>{{ ucfirst($enrollment->status) }}</strong>
                </p>

                @can('enrollments.update')
                <form method="POST" action="{{ route('university.enrollments.update-status', $enrollment) }}" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    @method('PATCH')

                    <select name="status" required class="rounded-md border-gray-300 flex-1">
                        <option value="pending" @selected($enrollment->status === 'pending')>Pendente</option>
                        <option value="active" @selected($enrollment->status === 'active')>Activa</option>
                        <option value="suspended" @selected($enrollment->status === 'suspended')>Suspensa</option>
                        <option value="cancelled" @selected($enrollment->status === 'cancelled')>Cancelada</option>
                        <option value="completed" @selected($enrollment->status === 'completed')>Concluída</option>
                    </select>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">
                        Actualizar estado
                    </button>
                </form>
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
