<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Gestão de Utilizadores
                </h2>
                <p class="text-sm text-gray-500">
                    Gerir os utilizadores da universidade.
                </p>
            </div>

            <a href="{{ route('university.users.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                + Novo Utilizador
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="rounded-lg bg-green-100 p-4 text-green-700">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="rounded-lg bg-red-100 p-4 text-red-700">
                <ul class="list-inside list-disc">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="rounded-xl bg-white p-5 shadow">

                <form method="GET" action="{{ route('university.users.index') }}" class="grid grid-cols-1 gap-3 md:grid-cols-4">

                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome ou email..." class="rounded-lg border-gray-300">

                    <select name="role" class="rounded-lg border-gray-300">
                        <option value="">Todos os perfis</option>

                        @foreach($roles as $role)
                        <option value="{{ $role->name }}" @selected(request('role')===$role->name)>
                            {{ $role->name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="status" class="rounded-lg border-gray-300">
                        <option value="">Todos os estados</option>
                        <option value="active" @selected(request('status')==='active' )>
                            Activos
                        </option>
                        <option value="inactive" @selected(request('status')==='inactive' )>
                            Desactivados
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <button class="flex-1 rounded-lg bg-indigo-600 px-4 py-2 text-white">
                            Pesquisar
                        </button>

                        <a href="{{ route('university.users.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-gray-700">
                            Limpar
                        </a>
                    </div>

                </form>

            </div>

            <div class="overflow-hidden rounded-xl bg-white shadow">

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Utilizador
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Perfil
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase text-gray-500">
                                    Estado
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase text-gray-500">
                                    Acções
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse($users as $user)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">
                                        {{ $user->name }}
                                    </div>

                                    <div class="text-sm text-gray-500">
                                        {{ $user->email }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $user->getRoleNames()->implode(', ') ?: 'Sem perfil' }}
                                </td>

                                <td class="px-6 py-4">
                                    @if($user->is_active)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Activo
                                    </span>
                                    @else
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Desactivado
                                    </span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">

                                        <a href="{{ route('university.users.edit', $user->id) }}" class="rounded bg-blue-100 px-3 py-1 text-sm text-blue-700">
                                            Editar
                                        </a>

                                        @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('university.users.status', $user->id) }}">
                                            @csrf
                                            @method('PATCH')

                                            <button class="rounded bg-yellow-100 px-3 py-1 text-sm text-yellow-700">
                                                {{ $user->is_active ? 'Desactivar' : 'Activar' }}
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('university.users.destroy', $user->id) }}" onsubmit="return confirm('Tem a certeza que deseja eliminar este utilizador?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="rounded bg-red-100 px-3 py-1 text-sm text-red-700">
                                                Eliminar
                                            </button>
                                        </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                    Nenhum utilizador encontrado.
                                </td>
                            </tr>
                            @endforelse

                        </tbody>

                    </table>
                </div>

                <div class="p-4">
                    {{ $users->links() }}
                </div>

            </div>

        </div>
    </div>

</x-app-layout>
