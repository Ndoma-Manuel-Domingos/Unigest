@csrf

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Nome completo *
        </label>

        <input type="text" name="name" value="{{ old('name', $user?->name) }}" required class="w-full rounded-lg border-gray-300">

        @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Email *
        </label>

        <input type="email" name="email" value="{{ old('email', $user?->email) }}" required class="w-full rounded-lg border-gray-300">

        @error('email')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Palavra-passe {{ $user ? '(deixar vazio para manter)' : '*' }}
        </label>

        <input type="password" name="password" {{ $user ? '' : 'required' }} minlength="8" autocomplete="new-password" class="w-full rounded-lg border-gray-300">

        @error('password')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Confirmar palavra-passe {{ $user ? '' : '*' }}
        </label>

        <input type="password" name="password_confirmation" {{ $user ? '' : 'required' }} minlength="8" autocomplete="new-password" class="w-full rounded-lg border-gray-300">
    </div>

    <div class="md:col-span-2">
        <label class="mb-1 block text-sm font-medium text-gray-700">
            Perfil de acesso *
        </label>

        <select name="role" required class="w-full rounded-lg border-gray-300">
            <option value="">Seleccione um perfil</option>

            @foreach($roles as $role)
            <option value="{{ $role->name }}" @selected(old('role', $user?->getRoleNames()->first()) === $role->name)
                >
                {{ $role->name }}
            </option>
            @endforeach
        </select>

        @error('role')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

</div>

<div class="mt-6 flex justify-end gap-3">

    <a href="{{ route('university.users.index') }}" class="rounded-lg bg-gray-100 px-5 py-2 text-gray-700">
        Cancelar
    </a>

    <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 font-semibold text-white hover:bg-indigo-700">
        {{ $user ? 'Actualizar utilizador' : 'Guardar utilizador' }}
    </button>

</div>
