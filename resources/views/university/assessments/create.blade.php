<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Criar avaliação</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
            <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">
                <ul class="list-disc ml-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('university.assessments.store', $academicClass) }}" class="bg-white shadow-sm rounded-lg p-6 space-y-5">
                @csrf

                <div class="p-4 bg-gray-50 rounded">
                    <p class="text-sm text-gray-500">Turma</p>
                    <p class="font-semibold">{{ $academicClass->name }}</p>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Disciplina *</label>
                    <select name="curriculum_subject_id" required class="w-full rounded-md border-gray-300">
                        <option value="">Seleccione a disciplina</option>
                        @foreach($subjects as $item)
                        <option value="{{ $item->id }}" @selected(old('curriculum_subject_id')==$item->id)>
                            {{ $item->subject->code }} — {{ $item->subject->name }}
                            ({{ $item->year_number }}.º ano / {{ $item->semester_number }}.º semestre)
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Nome da avaliação *</label>
                    <input type="text" name="title" required maxlength="150" value="{{ old('title') }}" placeholder="Ex.: Primeiro teste" class="w-full rounded-md border-gray-300">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tipo *</label>
                        <select name="type" required class="w-full rounded-md border-gray-300">
                            <option value="test">Teste</option>
                            <option value="work">Trabalho</option>
                            <option value="exam">Prova</option>
                            <option value="final_exam">Exame final</option>
                            <option value="participation">Participação</option>
                            <option value="other">Outro</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Data *</label>
                        <input type="date" name="assessment_date" required value="{{ old('assessment_date', date('Y-m-d')) }}" class="w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Ponderação (%) *</label>
                        <input type="number" name="weight" required min="0.01" max="100" step="0.01" value="{{ old('weight') }}" placeholder="Ex.: 30" class="w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Nota máxima *</label>
                        <input type="number" name="max_grade" required min="0.01" max="100" step="0.01" value="{{ old('max_grade', 20) }}" class="w-full rounded-md border-gray-300">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Descrição</label>
                    <textarea name="description" rows="3" class="w-full rounded-md border-gray-300">{{ old('description') }}</textarea>
                </div>

                <div class="flex justify-end gap-3 border-t pt-5">
                    <a href="{{ route('university.assessments.index', $academicClass) }}" class="px-4 py-2 border rounded-md">Cancelar</a>
                    <button class="px-5 py-2 bg-indigo-600 text-white rounded-md">
                        Guardar avaliação
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
