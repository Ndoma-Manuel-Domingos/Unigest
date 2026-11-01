<x-app-layout>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Perfis e Permissões
                </h2>
                <p class="text-sm text-gray-500">
                    Gestão dos níveis de acesso ao sistema.
                </p>
            </div>

            <a href="{{ route('university.roles.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-white">
                + Novo Perfil
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-5 rounded-lg bg-green-100 p-4 text-green-700">
                {{ session('success') }}
            </div>
            @endif

            <div class="overflow-hidden rounded-xl bg-white shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                    Perfil
                                </th>

                                <th class="px-6 py-3 text-left text-xs uppercase text-gray-500">
                                    Permissões
                                </th>

                                <th class="px-6 py-3 text-right text-xs uppercase text-gray-500">
                                    Acções
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($roles as $role)
                            <tr>
                                <td class="px-6 py-4 font-medium text-gray-800">
                                    {{ $role->name }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    {{ $role->permissions_count }}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">

                                        <a href="{{ route('university.roles.edit', $role->id) }}" class="rounded bg-blue-100 px-3 py-1 text-sm text-blue-700">
                                            Editar
                                        </a>

                                        <form method="POST" action="{{ route('university.roles.destroy', $role->id) }}" onsubmit="return confirm('Deseja eliminar este perfil?')">
                                            @csrf
                                            @method('DELETE')

                                            <button class="rounded bg-red-100 px-3 py-1 text-sm text-red-700">
                                                Eliminar
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                    Nenhum perfil encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>
                <div class="p-4">
                    {{ $roles->links() }}
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
