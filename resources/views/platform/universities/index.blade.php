<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Gestão de Universidades
            </h2>

            <a href="{{ route('platform.universities.create') }}" class="rounded-md bg-indigo-600 text-white px-4 py-2 text-sm font-semibold hover:bg-indigo-700">
                + Nova Universidade
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="rounded-md bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="rounded-md bg-red-100 p-4 text-red-800">
                <ul class="list-inside list-disc">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow">
                <form method="GET" action="{{ route('platform.universities.index') }}" class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Pesquisar
                        </label>

                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, email, NIF ou código..." class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700">
                            Estado
                        </label>

                        <select name="status" class="w-full rounded-md border-gray-300 shadow-sm">
                            <option value="">Todos os estados</option>

                            @foreach([
                            'pending' => 'Pendente',
                            'trial' => 'Período experimental',
                            'active' => 'Activa',
                            'suspended' => 'Suspensa',
                            'expired' => 'Expirada',
                            'cancelled' => 'Cancelada',
                            ] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status')===$value)>
                                {{ $label }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-end gap-2">
                        <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm text-white hover:bg-gray-900">
                            Pesquisar
                        </button>

                        <a href="{{ route('platform.universities.index') }}" class="rounded-md border px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                            Limpar
                        </a>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">
                                    Universidade
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">
                                    Contacto
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">
                                    Província
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase">
                                    Estado
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-semibold uppercase">
                                    Acções
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($universities as $university)
                            <tr>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-gray-900">
                                        {{ $university->name }}
                                    </div>

                                    <div class="text-sm text-gray-500">
                                        {{ $university->slug }}
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-600">
                                    <div>{{ $university->email ?: 'Sem email' }}</div>
                                    <div>{{ $university->phone ?: 'Sem telefone' }}</div>
                                </td>

                                <td class="px-4 py-4 text-sm text-gray-600">
                                    {{ $university->province ?: '—' }}
                                </td>

                                <td class="px-4 py-4">
                                    @php
                                    $statusLabels = [
                                    'pending' => 'Pendente',
                                    'trial' => 'Experimental',
                                    'active' => 'Activa',
                                    'suspended' => 'Suspensa',
                                    'expired' => 'Expirada',
                                    'cancelled' => 'Cancelada',
                                    ];
                                    @endphp

                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700">
                                        {{ $statusLabels[$university->status] ?? $university->status }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 text-right">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <a href="{{ route('platform.universities.edit', $university) }}" class="rounded bg-indigo-100 px-3 py-1 text-sm text-indigo-700 hover:bg-indigo-200">
                                            Editar
                                        </a>
                                        <a href="{{ route('platform.universities.administrators.index', $university) }}" class="rounded bg-green-100 px-3 py-1 text-sm text-green-700 hover:bg-green-200">
                                            Administradores
                                        </a>

                                        <form method="POST" action="{{ route('platform.universities.status', $university) }}" class="flex items-center gap-1">
                                            @csrf
                                            @method('PATCH')

                                            <select name="status" class="rounded-md border-gray-300 py-1 text-xs">
                                                @foreach($statusLabels as $value => $label)
                                                <option value="{{ $value }}" @selected($university->status === $value)>
                                                    {{ $label }}
                                                </option>
                                                @endforeach
                                            </select>

                                            <button type="submit" class="rounded bg-gray-800 px-3 py-1 text-sm text-white hover:bg-gray-900">
                                                Alterar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                    Nenhuma universidade encontrada.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-6">
                    {{ $universities->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
