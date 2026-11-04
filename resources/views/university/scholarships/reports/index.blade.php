<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Relatório de Bolsas
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Análise financeira e académica das bolsas de estudo.
                </p>
            </div>

            <a href="{{ route('university.scholarship-types.index') }}" class="text-sm text-indigo-600 hover:underline">
                Gestão de bolsas
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

            {{-- FILTROS --}}
            <div class="rounded-lg bg-white p-6 shadow">

                <form method="GET" action="{{ route('university.scholarships.reports') }}" class="grid grid-cols-1 gap-4 md:grid-cols-3">

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Data inicial
                        </label>

                        <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Data final
                        </label>

                        <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" class="mt-1 block w-full rounded-md border-gray-300">
                    </div>

                    <div class="flex items-end">
                        <button type="submit" class="rounded-md bg-indigo-600 px-5 py-2.5 text-sm font-medium text-white hover:bg-indigo-700">
                            Actualizar relatório
                        </button>
                    </div>

                </form>
            </div>

            {{-- INDICADORES --}}
            <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">

                <div class="rounded-lg bg-white p-6 shadow">
                    <p class="text-sm text-gray-500">
                        Bolsas aprovadas
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($approvedScholarships, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <p class="text-sm text-gray-500">
                        Estudantes beneficiários
                    </p>

                    <p class="mt-2 text-3xl font-bold text-gray-900">
                        {{ number_format($beneficiaryStudents, 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <p class="text-sm text-gray-500">
                        Descontos concedidos
                    </p>

                    <p class="mt-2 text-2xl font-bold text-red-600">
                        {{ number_format($totalDiscount, 2, ',', '.') }}
                        AOA
                    </p>
                </div>

                <div class="rounded-lg bg-white p-6 shadow">
                    <p class="text-sm text-gray-500">
                        Receita líquida
                    </p>

                    <p class="mt-2 text-2xl font-bold text-green-600">
                        {{ number_format($netInvoiced, 2, ',', '.') }}
                        AOA
                    </p>
                </div>

            </div>

            {{-- IMPACTO FINANCEIRO --}}
            <div class="rounded-lg bg-white p-6 shadow">

                <h3 class="text-lg font-semibold text-gray-800">
                    Impacto financeiro
                </h3>

                <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3">

                    <div>
                        <p class="text-sm text-gray-500">
                            Total bruto facturado
                        </p>

                        <p class="mt-1 text-xl font-semibold">
                            {{ number_format($grossInvoiced, 2, ',', '.') }}
                            AOA
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Total de descontos
                        </p>

                        <p class="mt-1 text-xl font-semibold text-red-600">
                            {{ number_format($totalDiscount, 2, ',', '.') }}
                            AOA
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">
                            Total líquido
                        </p>

                        <p class="mt-1 text-xl font-semibold text-green-600">
                            {{ number_format($netInvoiced, 2, ',', '.') }}
                            AOA
                        </p>
                    </div>

                </div>

            </div>

            {{-- ESTADOS --}}
            <div class="rounded-lg bg-white p-6 shadow">

                <h3 class="text-lg font-semibold text-gray-800">
                    Estado das bolsas
                </h3>

                <div class="mt-5 grid grid-cols-2 gap-4 md:grid-cols-5">

                    @php
                    $statuses = [
                    'pending' => ['label' => 'Pendentes', 'class' => 'text-yellow-600'],
                    'approved' => ['label' => 'Aprovadas', 'class' => 'text-green-600'],
                    'rejected' => ['label' => 'Rejeitadas', 'class' => 'text-red-600'],
                    'suspended' => ['label' => 'Suspensas', 'class' => 'text-gray-600'],
                    'expired' => ['label' => 'Expiradas', 'class' => 'text-orange-600'],
                    ];
                    @endphp

                    @foreach($statuses as $status => $item)
                    <div class="rounded-lg border p-4">
                        <p class="text-sm text-gray-500">
                            {{ $item['label'] }}
                        </p>

                        <p class="mt-1 text-2xl font-bold {{ $item['class'] }}">
                            {{ number_format($statusCounts[$status] ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                    @endforeach

                </div>

            </div>

            {{-- POR TIPO --}}
            <div class="overflow-hidden rounded-lg bg-white shadow">

                <div class="border-b p-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Bolsas por tipo
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs uppercase">
                                    Código
                                </th>

                                <th class="px-6 py-3 text-left text-xs uppercase">
                                    Bolsa
                                </th>

                                <th class="px-6 py-3 text-left text-xs uppercase">
                                    Tipo
                                </th>

                                <th class="px-6 py-3 text-left text-xs uppercase">
                                    Valor
                                </th>

                                <th class="px-6 py-3 text-right text-xs uppercase">
                                    Aprovadas
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">

                            @forelse($byType as $type)

                            <tr>

                                <td class="px-6 py-4 text-sm">
                                    {{ $type->code }}
                                </td>

                                <td class="px-6 py-4 text-sm font-medium">
                                    {{ $type->name }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    {{ $type->type === 'percentage'
                                            ? 'Percentagem'
                                            : 'Valor fixo' }}
                                </td>

                                <td class="px-6 py-4 text-sm">
                                    @if($type->type === 'percentage')
                                    {{ number_format($type->value, 2, ',', '.') }}%
                                    @else
                                    {{ number_format($type->value, 2, ',', '.') }} AOA
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-right text-sm font-semibold">
                                    {{ number_format($type->approved_count, 0, ',', '.') }}
                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    Nenhuma bolsa encontrada.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

            </div>

            {{-- EVOLUÇÃO MENSAL --}}
            <div class="overflow-hidden rounded-lg bg-white shadow">

                <div class="border-b p-6">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Descontos por mês
                    </h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs uppercase">
                                    Mês
                                </th>

                                <th class="px-6 py-3 text-right text-xs uppercase">
                                    Descontos
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">

                            @forelse($monthlyDiscounts as $row)

                            <tr>

                                <td class="px-6 py-4 text-sm">
                                    {{ $row->month
                                        ? \Carbon\Carbon::createFromFormat('Y-m', $row->month)->translatedFormat('F Y')
                                        : 'Data indisponível' }}
                                </td>

                                <td class="px-6 py-4 text-right text-sm font-semibold">
                                    {{ number_format($row->total, 2, ',', '.') }}
                                    AOA
                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="2" class="px-6 py-8 text-center text-gray-500">
                                    Não existem descontos no período seleccionado.
                                </td>
                            </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

            </div>

        </div>
    </div>

</x-app-layout>
