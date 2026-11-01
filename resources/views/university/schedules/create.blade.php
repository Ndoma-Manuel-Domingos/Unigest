<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Novo Horário
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                @if(session('error'))
                <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg">
                    {{ session('error') }}
                </div>
                @endif

                <form method="POST" action="{{ route('university.schedules.store', $academicClass) }}">

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium">
                                Disciplina
                            </label>

                            <select name="curriculum_subject_id" required class="mt-1 w-full rounded-lg border-gray-300">

                                <option value="">
                                    Seleccione
                                </option>

                                @foreach($subjects as $item)

                                <option value="{{ $item->id }}" @selected(old('curriculum_subject_id')==$item->id)>

                                    {{ $item->subject->name }}

                                </option>

                                @endforeach

                            </select>

                        </div>

                        <div>

                            <label class="block text-sm font-medium">
                                Professor
                            </label>

                            <select name="teacher_id" required class="mt-1 w-full rounded-lg border-gray-300">

                                <option value="">
                                    Seleccione
                                </option>

                                @foreach($teachers as $teacher)

                                <option value="{{ $teacher->id }}" @selected(old('teacher_id')==$teacher->id)>

                                    {{ $teacher->teacher_number }}
                                    -
                                    {{ $teacher->name }}

                                </option>

                                @endforeach

                            </select>

                        </div>

                        <div>

                            <label class="block text-sm font-medium">
                                Sala
                            </label>

                            <select name="room_id" class="mt-1 w-full rounded-lg border-gray-300">

                                <option value="">
                                    Sem sala
                                </option>

                                @foreach($rooms as $room)

                                <option value="{{ $room->id }}" @selected(old('room_id')==$room->id)>

                                    {{ $room->name }}
                                    @if($room->capacity)
                                    — {{ $room->capacity }} lugares
                                    @endif

                                </option>

                                @endforeach

                            </select>

                        </div>

                        <div>

                            <label class="block text-sm font-medium">
                                Dia da semana
                            </label>

                            <select name="day_of_week" required class="mt-1 w-full rounded-lg border-gray-300">

                                <option value="1">Segunda-feira</option>
                                <option value="2">Terça-feira</option>
                                <option value="3">Quarta-feira</option>
                                <option value="4">Quinta-feira</option>
                                <option value="5">Sexta-feira</option>
                                <option value="6">Sábado</option>
                                <option value="7">Domingo</option>

                            </select>

                        </div>

                        <div>

                            <label class="block text-sm font-medium">
                                Turno
                            </label>

                            <select name="shift" class="mt-1 w-full rounded-lg border-gray-300">

                                <option value="">Não definido</option>
                                <option value="manha">Manhã</option>
                                <option value="tarde">Tarde</option>
                                <option value="noite">Noite</option>
                                <option value="integral">Integral</option>
                                <option value="pos_laboral">Pós-laboral</option>

                            </select>

                        </div>

                        <div>

                            <label class="block text-sm font-medium">
                                Hora inicial
                            </label>

                            <input type="time" name="start_time" required value="{{ old('start_time') }}" class="mt-1 w-full rounded-lg border-gray-300">

                        </div>

                        <div>

                            <label class="block text-sm font-medium">
                                Hora final
                            </label>

                            <input type="time" name="end_time" required value="{{ old('end_time') }}" class="mt-1 w-full rounded-lg border-gray-300">

                        </div>

                        <div>

                            <label class="block text-sm font-medium">
                                Início da vigência
                            </label>

                            <input type="date" name="starts_at" value="{{ old('starts_at') }}" class="mt-1 w-full rounded-lg border-gray-300">

                        </div>

                        <div>

                            <label class="block text-sm font-medium">
                                Fim da vigência
                            </label>

                            <input type="date" name="ends_at" value="{{ old('ends_at') }}" class="mt-1 w-full rounded-lg border-gray-300">

                        </div>

                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium">
                                Observações
                            </label>

                            <textarea name="observations" rows="4" class="mt-1 w-full rounded-lg border-gray-300">{{ old('observations') }}</textarea>

                        </div>

                        <div class="md:col-span-2">

                            <input type="hidden" name="status" value="active">

                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-3">

                        <a href="{{ route('university.schedules.index', $academicClass) }}" class="px-4 py-2 bg-gray-200 rounded-lg">
                            Cancelar
                        </a>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Guardar Horário
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
