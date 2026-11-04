<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <h2 class="text-xl font-semibold text-gray-800">
                Tipos de bolsas de estudo
            </h2>

            @can('scholarships.types.create')
            <a href="{{ route('university.scholarship-types.create') }}" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white hover:bg-indigo-700">
                + Nova bolsa
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 rounded bg-green-100 p-3 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 rounded bg-red-100 p-3 text-red-800">
                {{ session('error') }}
            </div>
            @endif

            <div class="mb-6 rounded-lg bg-white p-4 shadow">
                <form method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-4">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Pesquisar nome ou código..." class="rounded-md border-gray-300">

                    <select name="type" class="rounded-md border-gray-300">
                        <option value="">Todos os tipos</option>
                        <option value="percentage" @selected(request('type')==='percentage' )>
                            Percentagem
                        </option>
                        <option value="fixed" @selected(request('type')==='fixed' )>
                            Valor fixo
                        </option>
                    </select>

                    <select name="status" class="rounded-md border-gray-300">
                        <option value="">Todos os estados</option>
                        <option value="active" @selected(request('status')==='active' )>
                            Ativas
                        </option>
                        <option value="inactive" @selected(request('status')==='inactive' )>
                            Inativas
                        </option>
                    </select>

                    <div class="flex gap-2">
                        <button class="rounded-md bg-gray-800 px-4 py-2 text-white">
                            Filtrar
                        </button>
                        <a href="{{ route('university.scholarship-types.index') }}" class="rounded-md border px-4 py-2">
                            Limpar
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Código</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Bolsa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Tipo</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Valor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Atribuições</th>
                                <th class="px-6 py-3 text-left text-xs font-medium uppercase">Estado</th>
                                <th class="px-6 py-3 text-right text-xs font-medium uppercase">Acções</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            @forelse($scholarships as $scholarship)
                            <tr>
                                <td class="px-6 py-4 text-sm">{{ $scholarship->code }}</td>

                                <td class="px-6 py-4 text-sm font-medium">
                                    {{ $scholarship->name }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    {{ $scholarship->type === 'percentage' ? 'Percentagem' : 'Valor fixo' }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    @if($scholarship->type === 'percentage')
                                    {{ number_format($scholarship->value, 2, ',', '.') }}%
                                    @else
                                    {{ number_format($scholarship->value, 2, ',', '.') }} AOA
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    {{ $scholarship->student_scholarships_count }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    @if($scholarship->is_active)
                                    <span class="rounded bg-green-100 px-2 py-1 text-green-700">Ativa</span>
                                    @else
                                    <span class="rounded bg-gray-100 px-2 py-1 text-gray-700">Inativa</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right text-sm">
                                    <div class="flex justify-end gap-3">
                                        @can('scholarships.types.view')
                                        <a class="text-indigo-600" href="{{ route('university.scholarship-types.show', $scholarship) }}">
                                            Ver
                                        </a>
                                        @endcan

                                        @can('scholarships.types.update')
                                        <a class="text-blue-600" href="{{ route('university.scholarship-types.edit', $scholarship) }}">
                                            Editar
                                        </a>
                                        @endcan

                                        @can('scholarships.types.delete')
                                        <form method="POST" action="{{ route('university.scholarship-types.destroy', $scholarship) }}" onsubmit="return confirm('Deseja eliminar este tipo de bolsa?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-red-600">Eliminar</button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    Nenhum tipo de bolsa encontrado.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-4">
                    {{ $scholarships->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
