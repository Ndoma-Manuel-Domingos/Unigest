<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800">Matrículas</h2>

            @can('enrollments.create')
            <a href="{{ route('university.enrollments.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md">
                Nova matrícula
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

            <div class="bg-white shadow-sm rounded-lg p-5 mb-5">
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="N.º matrícula, estudante..." class="rounded-md border-gray-300">

                    <select name="academic_year_id" class="rounded-md border-gray-300">
                        <option value="">Todos os anos académicos</option>
                        @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" @selected(request('academic_year_id')==$year->id)>
                            {{ $year->name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="status" class="rounded-md border-gray-300">
                        <option value="">Todos os estados</option>
                        @foreach([
                        'pending' => 'Pendente',
                        'active' => 'Activa',
                        'suspended' => 'Suspensa',
                        'cancelled' => 'Cancelada',
                        'completed' => 'Concluída'
                        ] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status')===$value)>
                            {{ $label }}
                        </option>
                        @endforeach
                    </select>

                    <button class="px-4 py-2 bg-gray-800 text-white rounded-md">
                        Filtrar
                    </button>
                </form>
            </div>

            <div class="bg-white shadow-sm rounded-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left">N.º Matrícula</th>
                            <th class="px-4 py-3 text-left">Estudante</th>
                            <th class="px-4 py-3 text-left">Curso</th>
                            <th class="px-4 py-3 text-left">Turma</th>
                            <th class="px-4 py-3 text-left">Ano Académico</th>
                            <th class="px-4 py-3 text-left">Data</th>
                            <th class="px-4 py-3 text-left">Estado</th>
                            <th class="px-4 py-3 text-right">Acções</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @forelse($enrollments as $enrollment)
                        <tr>
                            <td class="px-4 py-3 font-medium">
                                {{ $enrollment->enrollment_number }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $enrollment->student->name }}
                                <div class="text-xs text-gray-500">
                                    {{ $enrollment->student->student_number }}
                                </div>
                            </td>
                            <td class="px-4 py-3">{{ $enrollment->course->name }}</td>
                            <td class="px-4 py-3">{{ $enrollment->academicClass->name }}</td>
                            <td class="px-4 py-3">{{ $enrollment->academicYear->name }}</td>
                            <td class="px-4 py-3">
                                {{ $enrollment->enrollment_date?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                $badges = [
                                'pending' => 'bg-yellow-100 text-yellow-800',
                                'active' => 'bg-green-100 text-green-800',
                                'suspended' => 'bg-orange-100 text-orange-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                                'completed' => 'bg-blue-100 text-blue-800',
                                ];
                                $labels = [
                                'pending' => 'Pendente',
                                'active' => 'Activa',
                                'suspended' => 'Suspensa',
                                'cancelled' => 'Cancelada',
                                'completed' => 'Concluída',
                                ];
                                @endphp
                                <span class="px-2 py-1 rounded text-xs {{ $badges[$enrollment->status] ?? 'bg-gray-100' }}">
                                    {{ $labels[$enrollment->status] ?? $enrollment->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('university.enrollments.show', $enrollment) }}" class="text-indigo-600 hover:underline">
                                    Consultar
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                                Nenhuma matrícula encontrada.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="p-4">
                    {{ $enrollments->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
