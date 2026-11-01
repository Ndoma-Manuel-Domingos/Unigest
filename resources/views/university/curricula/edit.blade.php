<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Gestão do Plano Curricular
                </h2>
                <p class="text-sm text-gray-500">
                    {{ $curriculum->course->name }} — {{ $curriculum->name }}
                </p>
            </div>

            <a href="{{ route('university.curricula.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700">
                Voltar aos currículos
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- DADOS DO CURRÍCULO --}}
            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="mb-5 text-lg font-semibold text-gray-800">
                    Dados do currículo
                </h3>

                <form method="POST" action="{{ route('university.curricula.update', $curriculum) }}">
                    @include('university.curricula.form')
                </form>
            </div>

            {{-- RESUMO --}}
            @php
            $totalDisciplinas = $curriculum->subjects->count();
            $totalCreditos = $curriculum->subjects->sum('credits');
            $totalHoras = $curriculum->subjects->sum('total_hours');
            $totalObrigatorias = $curriculum->subjects
            ->where('type', 'obrigatoria')->count();
            @endphp

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-sm text-gray-500">Total de disciplinas</p>
                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ $totalDisciplinas }}
                    </p>
                </div>

                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-sm text-gray-500">Disciplinas obrigatórias</p>
                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ $totalObrigatorias }}
                    </p>
                </div>

                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-sm text-gray-500">Total de créditos</p>
                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ $totalCreditos }}
                    </p>
                </div>

                <div class="rounded-lg bg-white p-5 shadow">
                    <p class="text-sm text-gray-500">Carga horária total</p>
                    <p class="mt-2 text-2xl font-bold text-gray-800">
                        {{ $totalHoras }} h
                    </p>
                </div>
            </div>

            {{-- ADICIONAR DISCIPLINA --}}
            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="mb-1 text-lg font-semibold text-gray-800">
                    Adicionar disciplina
                </h3>

                <p class="mb-5 text-sm text-gray-500">
                    Seleccione uma disciplina e defina a sua posição no currículo.
                </p>

                @if($availableSubjects->isEmpty())
                <div class="rounded-md bg-yellow-50 p-4 text-sm text-yellow-800">
                    Não existem disciplinas disponíveis. Crie disciplinas no módulo
                    Disciplinas ou verifique se já foram todas associadas a este currículo.
                </div>
                @else
                <form method="POST" action="{{ route('university.curricula.subjects.store', $curriculum) }}">
                    @csrf

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Disciplina
                            </label>

                            <select name="subject_id" id="subject_id" class="w-full rounded-md border-gray-300" required>
                                <option value="">Seleccione uma disciplina</option>

                                @foreach($availableSubjects as $subject)
                                <option value="{{ $subject->id }}" data-credits="{{ $subject->credits }}" data-theoretical="{{ $subject->theoretical_hours }}" data-practical="{{ $subject->practical_hours }}" data-laboratory="{{ $subject->laboratory_hours }}">
                                    {{ $subject->code }} — {{ $subject->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Ano curricular
                            </label>
                            <select name="year_number" class="w-full rounded-md border-gray-300" required>
                                <option value="">Seleccione</option>
                                @for($year = 1; $year <= 10; $year++) <option value="{{ $year }}">{{ $year }}.º Ano</option>
                                    @endfor
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Semestre
                            </label>
                            <select name="semester_number" class="w-full rounded-md border-gray-300" required>
                                <option value="">Seleccione</option>
                                <option value="1">1.º Semestre</option>
                                <option value="2">2.º Semestre</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Tipo
                            </label>
                            <select name="type" class="w-full rounded-md border-gray-300" required>
                                <option value="obrigatoria">Obrigatória</option>
                                <option value="opcional">Opcional</option>
                                <option value="electiva">Electiva</option>
                            </select>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Créditos
                            </label>
                            <input type="number" name="credits" id="credits" min="0" max="100" value="0" class="w-full rounded-md border-gray-300" required>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Horas teóricas
                            </label>
                            <input type="number" name="theoretical_hours" id="theoretical_hours" min="0" max="1000" value="0" class="w-full rounded-md border-gray-300" required>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Horas práticas
                            </label>
                            <input type="number" name="practical_hours" id="practical_hours" min="0" max="1000" value="0" class="w-full rounded-md border-gray-300" required>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Horas de laboratório
                            </label>
                            <input type="number" name="laboratory_hours" id="laboratory_hours" min="0" max="1000" value="0" class="w-full rounded-md border-gray-300" required>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Ordem de apresentação
                            </label>
                            <input type="number" name="order_number" min="0" value="0" class="w-full rounded-md border-gray-300">
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button type="submit" class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Adicionar disciplina
                        </button>
                    </div>
                </form>
                @endif
            </div>

            {{-- DISCIPLINAS ORGANIZADAS POR ANO E SEMESTRE --}}
            <div class="space-y-5">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">
                        Disciplinas do currículo
                    </h3>
                    <p class="text-sm text-gray-500">
                        Edite os dados, associe pré-requisitos ou remova disciplinas.
                    </p>
                </div>

                @forelse($curriculum->subjects->groupBy('year_number') as $year => $yearSubjects)
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b bg-gray-50 px-6 py-4">
                        <h4 class="font-semibold text-gray-800">
                            {{ $year }}.º Ano
                        </h4>
                    </div>

                    @foreach($yearSubjects->groupBy('semester_number') as $semester => $semesterSubjects)
                    <div class="border-b p-5 last:border-b-0">
                        <h5 class="mb-4 font-semibold text-indigo-700">
                            {{ $semester }}.º Semestre
                        </h5>

                        <div class="space-y-5">
                            @foreach($semesterSubjects as $item)
                            <div class="rounded-lg border border-gray-200 p-4">

                                <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                                    <div>
                                        <h6 class="font-semibold text-gray-900">
                                            {{ $item->subject->name }}
                                        </h6>
                                        <p class="text-sm text-gray-500">
                                            Código: {{ $item->subject->code }}
                                            · {{ $item->credits }} créditos
                                            · {{ $item->total_hours }} horas
                                        </p>

                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="rounded-full bg-blue-100 px-2 py-1 text-xs text-blue-700">
                                                {{ ucfirst($item->type) }}
                                            </span>

                                            <span class="rounded-full px-2 py-1 text-xs
                                                            {{ $item->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $item->is_active ? 'Activa' : 'Inactiva' }}
                                            </span>
                                        </div>
                                    </div>

                                    <form method="POST" action="{{ route('university.curricula.subjects.destroy', [$curriculum, $item]) }}" onsubmit="return confirm('Remover esta disciplina do currículo?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="rounded-md bg-red-100 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-200">
                                            Remover
                                        </button>
                                    </form>
                                </div>

                                {{-- EDITAR DISCIPLINA --}}
                                <form method="POST" action="{{ route('university.curricula.subjects.update', [$curriculum, $item]) }}" class="rounded-md bg-gray-50 p-4">
                                    @csrf
                                    @method('PUT')

                                    <div class="grid grid-cols-2 gap-3 md:grid-cols-4 lg:grid-cols-5">

                                        <div>
                                            <label class="text-xs font-medium text-gray-600">Ano</label>
                                            <input type="number" name="year_number" min="1" max="20" value="{{ $item->year_number }}" class="mt-1 w-full rounded-md border-gray-300 text-sm" required>
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-600">Semestre</label>
                                            <select name="semester_number" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                                <option value="1" @selected($item->semester_number == 1)>1.º</option>
                                                <option value="2" @selected($item->semester_number == 2)>2.º</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-600">Tipo</label>
                                            <select name="type" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                                <option value="obrigatoria" @selected($item->type === 'obrigatoria')>Obrigatória</option>
                                                <option value="opcional" @selected($item->type === 'opcional')>Opcional</option>
                                                <option value="electiva" @selected($item->type === 'electiva')>Electiva</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-600">Créditos</label>
                                            <input type="number" name="credits" min="0" max="100" value="{{ $item->credits }}" class="mt-1 w-full rounded-md border-gray-300 text-sm" required>
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-600">Ordem</label>
                                            <input type="number" name="order_number" min="0" value="{{ $item->order_number }}" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-600">Horas teóricas</label>
                                            <input type="number" name="theoretical_hours" min="0" max="1000" value="{{ $item->theoretical_hours }}" class="mt-1 w-full rounded-md border-gray-300 text-sm" required>
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-600">Horas práticas</label>
                                            <input type="number" name="practical_hours" min="0" max="1000" value="{{ $item->practical_hours }}" class="mt-1 w-full rounded-md border-gray-300 text-sm" required>
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-600">Horas laboratório</label>
                                            <input type="number" name="laboratory_hours" min="0" max="1000" value="{{ $item->laboratory_hours }}" class="mt-1 w-full rounded-md border-gray-300 text-sm" required>
                                        </div>

                                        <div>
                                            <label class="text-xs font-medium text-gray-600">Estado</label>
                                            <select name="is_active" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                                <option value="1" @selected($item->is_active)>Activa</option>
                                                <option value="0" @selected(!$item->is_active)>Inactiva</option>
                                            </select>
                                        </div>

                                        <div class="flex items-end">
                                            <button type="submit" class="w-full rounded-md bg-indigo-600 px-3 py-2 text-xs font-semibold text-white hover:bg-indigo-700">
                                                Guardar alterações
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                {{-- PRÉ-REQUISITOS EXISTENTES --}}
                                <div class="mt-4">
                                    <p class="mb-2 text-sm font-semibold text-gray-700">
                                        Pré-requisitos
                                    </p>

                                    @forelse($item->prerequisites as $prerequisite)
                                    <div class="mb-2 flex items-center justify-between rounded-md bg-yellow-50 px-3 py-2 text-sm">
                                        <span>
                                            {{ $prerequisite->prerequisiteSubject->subject->code }}
                                            —
                                            {{ $prerequisite->prerequisiteSubject->subject->name }}
                                        </span>

                                        <form method="POST" action="{{ route('university.curricula.subjects.prerequisites.destroy', [$curriculum, $item, $prerequisite]) }}">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" class="text-xs font-semibold text-red-600 hover:text-red-800">
                                                Remover
                                            </button>
                                        </form>
                                    </div>
                                    @empty
                                    <p class="text-xs text-gray-500">
                                        Nenhum pré-requisito definido.
                                    </p>
                                    @endforelse
                                </div>

                                {{-- ADICIONAR PRÉ-REQUISITO --}}
                                <form method="POST" action="{{ route('university.curricula.subjects.prerequisites.store', [$curriculum, $item]) }}" class="mt-3 flex flex-col gap-2 sm:flex-row">
                                    @csrf

                                    <select name="prerequisite_curriculum_subject_id" class="w-full rounded-md border-gray-300 text-sm" required>
                                        <option value="">Seleccionar pré-requisito</option>

                                        @foreach($curriculum->subjects->where('id', '!=', $item->id) as $candidate)
                                        <option value="{{ $candidate->id }}">
                                            {{ $candidate->year_number }}.º ano /
                                            {{ $candidate->semester_number }}.º semestre —
                                            {{ $candidate->subject->name }}
                                        </option>
                                        @endforeach
                                    </select>

                                    <button type="submit" class="rounded-md bg-yellow-600 px-4 py-2 text-xs font-semibold text-white hover:bg-yellow-700">
                                        Adicionar pré-requisito
                                    </button>
                                </form>

                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @empty
                <div class="rounded-lg bg-white p-8 text-center text-sm text-gray-500 shadow">
                    Ainda não existem disciplinas neste currículo.
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subjectSelect = document.getElementById('subject_id');

            if (!subjectSelect) {
                return;
            }

            subjectSelect.addEventListener('change', function() {
                const option = this.options[this.selectedIndex];
                document.getElementById('credits').value = option.dataset.credits || 0;
                document.getElementById('theoretical_hours').value = option.dataset.theoretical || 0;
                document.getElementById('practical_hours').value = option.dataset.practical || 0;
                document.getElementById('laboratory_hours').value = option.dataset.laboratory || 0;
            });
        });

    </script>
</x-app-layout>
