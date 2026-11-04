<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Atribuir bolsa a estudante
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow">

                <form method="POST" action="{{ route('university.student-scholarships.store') }}">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                        <div class="md:col-span-2">
                            <x-input-label for="student_id" value="Estudante" />

                            <select name="student_id" id="student_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="">Seleccione o estudante</option>
                                @foreach($students as $student)
                                <option value="{{ $student->id }}" @selected(old('student_id')==$student->id)>
                                    {{ $student->name }}
                                </option>
                                @endforeach
                            </select>

                            <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="scholarship_type_id" value="Tipo de bolsa" />

                            <select name="scholarship_type_id" id="scholarship_type_id" class="mt-1 block w-full rounded-md border-gray-300" required>
                                <option value="">Seleccione a bolsa</option>
                                @foreach($scholarshipTypes as $type)
                                <option value="{{ $type->id }}" @selected(old('scholarship_type_id')==$type->id)>
                                    {{ $type->name }} —
                                    {{ $type->type === 'percentage'
                                            ? number_format($type->value, 2, ',', '.') . '%'
                                            : number_format($type->value, 2, ',', '.') . ' AOA' }}
                                </option>
                                @endforeach
                            </select>

                            <x-input-error :messages="$errors->get('scholarship_type_id')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="academic_year_id" value="Ano académico" />

                            <select name="academic_year_id" id="academic_year_id" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Sem ano académico específico</option>
                                @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" @selected(old('academic_year_id')==$year->id)>
                                    {{ $year->name }}
                                </option>
                                @endforeach
                            </select>

                            <x-input-error :messages="$errors->get('academic_year_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="start_date" value="Data de início" />
                            <x-text-input type="date" id="start_date" name="start_date" class="mt-1 block w-full" :value="old('start_date', now()->format('Y-m-d'))" required />
                            <x-input-error :messages="$errors->get('start_date')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="end_date" value="Data de fim" />
                            <x-text-input type="date" id="end_date" name="end_date" class="mt-1 block w-full" :value="old('end_date')" />
                            <x-input-error :messages="$errors->get('end_date')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="application_reason" value="Justificação do pedido" />
                            <textarea name="application_reason" id="application_reason" rows="4" class="mt-1 block w-full rounded-md border-gray-300">{{ old('application_reason') }}</textarea>
                            <x-input-error :messages="$errors->get('application_reason')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('university.student-scholarships.index') }}" class="rounded-md border px-4 py-2 text-sm">
                            Cancelar
                        </a>

                        <x-primary-button>
                            Registar pedido
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
