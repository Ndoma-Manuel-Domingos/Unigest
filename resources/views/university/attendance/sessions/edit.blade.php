<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Editar Aula
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <form method="POST" action="{{ route('university.attendance.sessions.update', [
                          $academicClass,
                          $session
                      ]) }}">

                    @csrf
                    @method('PUT')

                    <div class="space-y-6">

                        <div>
                            <label class="block text-sm font-medium">
                                Disciplina
                            </label>

                            <select name="curriculum_subject_id" required class="mt-1 block w-full rounded-lg border-gray-300">

                                @foreach($subjects as $subject)

                                <option value="{{ $subject->id }}" @selected($session->curriculum_subject_id == $subject->id)>

                                    {{ $subject->year_number }}º Ano /
                                    {{ $subject->semester_number }}º Semestre -
                                    {{ $subject->subject->name }}

                                </option>

                                @endforeach

                            </select>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label class="block text-sm font-medium">
                                    Data
                                </label>

                                <input type="date" name="session_date" value="{{ old('session_date', $session->session_date?->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-lg border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium">
                                    Estado
                                </label>

                                <select name="status" class="mt-1 block w-full rounded-lg border-gray-300">

                                    <option value="scheduled" @selected($session->status === 'scheduled')}>
                                        Agendada
                                    </option>

                                    <option value="completed" @selected($session->status === 'completed')}>
                                        Concluída
                                    </option>

                                    <option value="cancelled" @selected($session->status === 'cancelled')}>
                                        Cancelada
                                    </option>

                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium">
                                    Hora início
                                </label>

                                <input type="time" name="start_time" value="{{ $session->start_time }}" class="mt-1 block w-full rounded-lg border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium">
                                    Hora fim
                                </label>

                                <input type="time" name="end_time" value="{{ $session->end_time }}" class="mt-1 block w-full rounded-lg border-gray-300">
                            </div>

                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Tema
                            </label>

                            <input type="text" name="topic" value="{{ old('topic', $session->topic) }}" class="mt-1 block w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Conteúdo
                            </label>

                            <textarea name="content" rows="5" class="mt-1 block w-full rounded-lg border-gray-300">{{ old('content', $session->content) }}</textarea>
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-3">

                        <a href="{{ route('university.attendance.sessions.index', $academicClass) }}" class="px-4 py-2 bg-gray-200 rounded-lg">
                            Cancelar
                        </a>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Actualizar
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
