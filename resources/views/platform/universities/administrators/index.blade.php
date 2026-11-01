<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Administradores da Universidade
                </h2>

                <p class="text-sm text-gray-500">
                    {{ $university->name }}
                </p>
            </div>

            <a href="{{ route('platform.universities.administrators.create', $university) }}" class="rounded-md text-white bg-indigo-600 px-4 py-2 text-sm font-semibold  hover:bg-indigo-700">
                + Novo Administrador
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="rounded-md bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-800">
                            {{ $university->name }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            Administradores associados a esta instituição.
                        </p>
                    </div>
                    <a href="{{ route('platform.universities.index') }}" class="text-sm  text-indigo-600 hover:underline">
                        Voltar às universidades
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Nome</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Email</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">Estado</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase">Acções</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($administrators as $administrator)
                            <tr>
                                <td class="px-4 py-4 font-medium text-gray-900">
                                    {{ $administrator->name }}
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-600">
                                    {{ $administrator->email }}
                                </td>

                                <td class="px-4 py-4">
                                    @if($administrator->is_active)
                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                                        Activo
                                    </span>
                                    @else
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700">
                                        Desactivado
                                    </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-right">
                                    <form method="POST" action="{{ route('platform.universities.administrators.status', [$university, $administrator->id]) }}" onsubmit="return confirm('Confirma esta alteração?')">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="is_active" value="{{ $administrator->is_active ? 0 : 1 }}">
                                        <button type="submit" class="rounded-md px-3 py-1 text-sm font-medium {{ $administrator->is_active ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                            {{ $administrator->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                    Ainda não existem administradores nesta universidade.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $administrators->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
