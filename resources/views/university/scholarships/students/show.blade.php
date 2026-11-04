<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                Detalhes da bolsa do estudante
            </h2>

            <a href="{{ route('university.student-scholarships.index') }}" class="text-sm text-indigo-600">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="rounded bg-green-100 p-3 text-green-800">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="rounded bg-red-100 p-3 text-red-800">
                {{ session('error') }}
            </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="text-lg font-semibold">Informações do pedido</h3>

                <div class="mt-5 grid grid-cols-1 gap-5 md:grid-cols-2">
                    <div>
                        <p class="text-sm text-gray-500">Estudante</p>
                        <p class="font-medium">
                            {{ $studentScholarship->student->name ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Tipo de bolsa</p>
                        <p class="font-medium">
                            {{ $studentScholarship->scholarshipType->name ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Ano académico</p>
                        <p class="font-medium">
                            {{ $studentScholarship->academicYear->name ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Valor da bolsa</p>
                        <p class="font-medium">
                            @if($studentScholarship->scholarshipType)
                            @if($studentScholarship->scholarshipType->type === 'percentage')
                            {{ number_format($studentScholarship->scholarshipType->value, 2, ',', '.') }}%
                            @else
                            {{ number_format($studentScholarship->approved_value ?? $studentScholarship->scholarshipType->value, 2, ',', '.') }} AOA
                            @endif
                            @else
                            —
                            @endif
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Data de início</p>
                        <p class="font-medium">
                            {{ $studentScholarship->start_date?->format('d/m/Y') ?? '—' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Data de fim</p>
                        <p class="font-medium">
                            {{ $studentScholarship->end_date?->format('d/m/Y') ?? 'Sem limite' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Estado</p>
                        <p class="font-medium">{{ ucfirst($studentScholarship->status) }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500">Decidido por</p>
                        <p class="font-medium">
                            {{ $studentScholarship->approver->name ?? '—' }}
                        </p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Justificação</p>
                        <p>{{ $studentScholarship->application_reason ?: 'Sem justificação.' }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">Observações da decisão</p>
                        <p>{{ $studentScholarship->decision_notes ?: 'Sem observações.' }}</p>
                    </div>
                </div>
            </div>

            @if($studentScholarship->status === 'pending')
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">

                @can('scholarships.students.approve')
                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="font-semibold text-green-700">Aprovar pedido</h3>
                    <p class="mt-2 text-sm text-gray-500">
                        Confirma a atribuição desta bolsa ao estudante.
                    </p>

                    <form method="POST" action="{{ route('university.student-scholarships.approve', $studentScholarship) }}" class="mt-4" onsubmit="return confirm('Confirma a aprovação desta bolsa?')">
                        @csrf

                        <button class="rounded-md bg-green-600 px-4 py-2 text-sm text-white hover:bg-green-700">
                            Aprovar bolsa
                        </button>
                    </form>
                </div>
                @endcan

                @can('scholarships.students.reject')
                <div class="rounded-lg bg-white p-6 shadow">
                    <h3 class="font-semibold text-red-700">Rejeitar pedido</h3>

                    <form method="POST" action="{{ route('university.student-scholarships.reject', $studentScholarship) }}" class="mt-4" onsubmit="return confirm('Confirma a rejeição deste pedido?')">
                        @csrf

                        <label class="block text-sm font-medium text-gray-700">
                            Motivo da rejeição
                        </label>

                        <textarea name="decision_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300" placeholder="Indique o motivo, se aplicável..."></textarea>

                        <button class="mt-3 rounded-md bg-red-600 px-4 py-2 text-sm text-white hover:bg-red-700">
                            Rejeitar pedido
                        </button>
                    </form>
                </div>
                @endcan
            </div>
            @endif

            @if($studentScholarship->status === 'approved')
            @can('scholarships.students.suspend')
            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="font-semibold text-gray-800">Suspender bolsa</h3>

                <form method="POST" action="{{ route('university.student-scholarships.suspend', $studentScholarship) }}" class="mt-4" onsubmit="return confirm('Confirma a suspensão desta bolsa?')">
                    @csrf

                    <label class="block text-sm font-medium text-gray-700">
                        Motivo da suspensão
                    </label>

                    <textarea name="decision_notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300"></textarea>

                    <button class="mt-3 rounded-md bg-gray-700 px-4 py-2 text-sm text-white">
                        Suspender bolsa
                    </button>
                </form>
            </div>
            @endcan
            @endif

            <div class="rounded-lg bg-white p-6 shadow">
                <h3 class="font-semibold">Facturas associadas</h3>

                <div class="mt-4 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs uppercase">Factura</th>
                                <th class="px-4 py-3 text-left text-xs uppercase">Data</th>
                                <th class="px-4 py-3 text-left text-xs uppercase">Desconto</th>
                                <th class="px-4 py-3 text-left text-xs uppercase">Estado</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y">
                            @forelse($studentScholarship->invoices as $invoice)
                            <tr>
                                <td class="px-4 py-3 text-sm">
                                    {{ $invoice->invoice_number }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $invoice->created_at?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ number_format($invoice->discount ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ ucfirst($invoice->status) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                                    Ainda não existem facturas associadas a esta bolsa.
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
