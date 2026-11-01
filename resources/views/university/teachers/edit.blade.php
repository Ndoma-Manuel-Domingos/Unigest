<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Novo Professor
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <form method="POST" action="{{ route('university.teachers.update', $teacher) }}">

                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium">
                                Nome completo
                            </label>

                            <input type="text" name="name" value="{{ old('name', $teacher->name) }}" required class="mt-1 w-full rounded-lg border-gray-300">

                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Nº funcionário
                            </label>

                            <input type="text" name="employee_number" value="{{ old('employee_number', $teacher->employee_number) }}" class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Género
                            </label>

                            <select name="gender" class="mt-1 w-full rounded-lg border-gray-300">
                                <option value="">Seleccione</option>
                                <option value="masculino" {{ $teacher->gender == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="feminino" {{ $teacher->gender == 'feminino' ? 'selected' : '' }}>Feminino</option>
                                <option value="outro" {{ $teacher->gender == 'outro' ? 'selected' : '' }}>Outro</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Data de nascimento
                            </label>

                            <input type="date" name="birth_date" value="{{ old('birth_date', $teacher->birth_date) }}" class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Telefone
                            </label>

                            <input type="text" name="phone" value="{{ old('phone', $teacher->phone) }}" class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Email
                            </label>

                            <input type="email" name="email" value="{{ old('email', $teacher->email) }}" class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Título académico
                            </label>

                            <input type="text" name="academic_title" value="{{ old('academic_title', $teacher->academic_title) }}" placeholder="Ex.: Mestre" class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Especialização
                            </label>

                            <input type="text" name="specialization" value="{{ old('specialization', $teacher->specialization) }}" class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium">
                                Morada
                            </label>

                            <textarea name="address" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ old('address', $teacher->address) }}</textarea>

                        </div>

                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium">
                                Observações
                            </label>

                            <textarea name="observations" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ old('observations', $teacher->observations) }}</textarea>

                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-3">

                        <a href="{{ route('university.teachers.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">
                            Cancelar
                        </a>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Guardar Professor
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
