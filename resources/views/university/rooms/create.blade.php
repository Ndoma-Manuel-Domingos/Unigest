<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Nova Sala
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow rounded-lg p-6">

                <form method="POST" action="{{ route('university.rooms.store') }}">

                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium">
                                Nome da sala
                            </label>

                            <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ex.: Sala 101" class="mt-1 w-full rounded-lg border-gray-300">

                            @error('name')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Código
                            </label>

                            <input type="text" name="code" value="{{ old('code') }}" placeholder="Ex.: S101" class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Tipo
                            </label>

                            <select name="type" class="mt-1 w-full rounded-lg border-gray-300">

                                <option value="classroom">
                                    Sala de Aula
                                </option>

                                <option value="laboratory">
                                    Laboratório
                                </option>

                                <option value="auditorium">
                                    Auditório
                                </option>

                                <option value="office">
                                    Gabinete
                                </option>

                                <option value="other">
                                    Outro
                                </option>

                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Edifício
                            </label>

                            <input type="text" name="building" value="{{ old('building') }}" class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Piso
                            </label>

                            <input type="text" name="floor" value="{{ old('floor') }}" class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">
                                Capacidade
                            </label>

                            <input type="number" name="capacity" value="{{ old('capacity') }}" min="1" class="mt-1 w-full rounded-lg border-gray-300">
                        </div>

                        <div class="md:col-span-2">

                            <label class="block text-sm font-medium">
                                Descrição
                            </label>

                            <textarea name="description" rows="4" class="mt-1 w-full rounded-lg border-gray-300">{{ old('description') }}</textarea>

                        </div>

                        <div class="md:col-span-2">

                            <label class="inline-flex items-center">

                                <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300">

                                <span class="ml-2">
                                    Sala activa
                                </span>

                            </label>

                        </div>

                    </div>

                    <div class="mt-6 flex justify-end gap-3">

                        <a href="{{ route('university.rooms.index') }}" class="px-4 py-2 bg-gray-200 rounded-lg">
                            Cancelar
                        </a>

                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                            Guardar
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
