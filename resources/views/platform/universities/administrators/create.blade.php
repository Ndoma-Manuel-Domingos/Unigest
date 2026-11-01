<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Criar Administrador
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="mb-5 rounded-lg bg-white p-5 shadow">
                <p class="text-sm text-gray-500">
                    Universidade
                </p>
                <h3 class="text-lg font-semibold text-gray-800">
                    {{ $university->name }}
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    O administrador ficará automaticamente associado a esta instituição.
                </p>
            </div>

            <div class="rounded-lg bg-white p-6 shadow">

                @if($errors->any())
                <div class="mb-5 rounded-md bg-red-100 p-4 text-red-800">
                    <ul class="list-inside list-disc">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('platform.universities.administrators.store', $university) }}">
                    @csrf
                    <div class="space-y-5">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Nome completo *
                            </label>

                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-md border-gray-300 shadow-sm">

                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Email *
                            </label>

                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-md border-gray-300 shadow-sm">

                            @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Palavra-passe *
                            </label>

                            <input type="password" name="password" required autocomplete="new-password" class="w-full rounded-md border-gray-300 shadow-sm">

                            @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700">
                                Confirmar palavra-passe *
                            </label>

                            <input type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('platform.universities.administrators.index', $university) }}" class="rounded-md border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </a>

                        <button type="submit" class="rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                            Criar Administrador
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
