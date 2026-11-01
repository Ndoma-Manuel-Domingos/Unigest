@csrf

<div>
    <label class="mb-2 block text-sm font-medium text-gray-700">
        Nome do perfil *
    </label>

    <input type="text" name="name" value="{{ old('name', $role?->name) }}" required class="w-full rounded-lg border-gray-300" placeholder="Ex.: Coordenador Académico">

    @error('name')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<div class="mt-6">
    <h3 class="mb-3 font-semibold text-gray-800">
        Permissões disponíveis
    </h3>

    @error('permissions')
    <p class="mb-3 text-sm text-red-600">{{ $message }}</p>
    @enderror

    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

        @foreach($permissions as $group => $items)
        <div class="rounded-lg border border-gray-200 p-4">

            <h4 class="mb-3 font-semibold capitalize text-indigo-700">
                {{ $group }}
            </h4>

            <div class="space-y-2">

                @foreach($items as $permission)
                <label class="flex items-center gap-2 text-sm text-gray-700">

                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" @checked( in_array( $permission->id,
                    old('permissions', $selectedPermissions ?? [])
                    )
                    )
                    class="rounded border-gray-300 text-indigo-600"
                    >

                    {{ $permission->name }}

                </label>
                @endforeach

            </div>

        </div>
        @endforeach

    </div>
</div>

<div class="mt-6 flex justify-end gap-3">

    <a href="{{ route('university.roles.index') }}" class="rounded-lg bg-gray-100 px-5 py-2 text-gray-700">
        Cancelar
    </a>

    <button type="submit" class="rounded-lg bg-indigo-600 px-5 py-2 text-white">
        {{ $role ? 'Actualizar Perfil' : 'Guardar Perfil' }}
    </button>

</div>
