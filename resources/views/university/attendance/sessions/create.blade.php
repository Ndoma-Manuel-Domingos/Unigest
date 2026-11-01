<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Nova Aula
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <form method="POST" action="{{ route('university.attendance.sessions.store', $academicClass) }}">

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium">
                                Disciplina
                            </label>

                            <select name="curriculum_subject_id" required class="mt-1 block w-full rounded-lg border-gray-300">

                                <option value="">
                                    Seleccione a disciplina
                                </option>

                                @foreach($subjects as $subject)

                                <option value="{{ $subject->id }}" @selected(old('curriculum_subject_id')==$subject->id)>

                                    {{ $subject->year_number }}º Ano /
                                    {{ $subject->semester_number }}º Semestre -
                                    {{ $subject->subject->name }}

                                </option>

                                @endforeach

                            </select>

                            @error('curriculum_subject_id')
                            <p class="text-red-600 text-sm mt-1">
                                {{ $message }}
                            </p>
                            @enderror

                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Data
                            </label>

                            <input type="date" name="session_date" value="{{ old('session_date', now()->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-lg border-gray-300">

                            @error('session_date')
                            <p class="text-red-600 text-sm">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Estado
                            </label>

                            <select name="status" class="mt-1 block w-full rounded-lg border-gray-300">

                                <option value="scheduled">
                                    Agendada
                                </option>

                                <option value="completed">
                                    Concluída
                                </option>

                                <option value="cancelled">
                                    Cancelada
                                </option>

                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Hora de início
                            </label>

                            <input type="time" name="start_time" value="{{ old('start_time') }}" class="mt-1 block w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Hora de fim
                            </label>

                            <input type="time" name="end_time" value="{{ old('end_time') }}" class="mt-1 block w-full rounded-lg border-gray-300">
                        </div>

                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium">
                                Tema da aula
                            </label>

                            <input type="text" name="topic" value="{{ old('topic') }}" maxlength="255" class="mt-1 block w-full rounded-lg border-gray-300" placeholder="Ex.: Introdução à Álgebra Linear">

                        </div>

                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium">
                                Conteúdo ministrado
                            </label>

                            <textarea name="content" rows="5" class="mt-1 block w-full rounded-lg border-gray-300">{{ old('content') }}</textarea>

                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-3">

                        <a href="{{ route('university.attendance.sessions.index', $academicClass) }}" class="px-4 py-2 bg-gray-200 rounded-lg">
                            Cancelar
                        </a>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Guardar Aula
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
