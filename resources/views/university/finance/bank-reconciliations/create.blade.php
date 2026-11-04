<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Nova Reconciliação Bancária
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    Compare os movimentos do sistema com o extrato bancário.
                </p>
            </div>

            <a href="{{ route('university.bank-reconciliations.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
                <div class="font-semibold text-red-800">
                    Corrija os seguintes erros:
                </div>

                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="bg-white shadow-sm rounded-xl border border-gray-200">

                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Dados da Reconciliação
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Informe a conta, período e saldos apresentados pelo banco.
                    </p>
                </div>

                <form method="POST" action="{{ route('university.bank-reconciliations.store') }}" class="p-6">

                    @csrf

                    @include(
                    'university.finance.bank-reconciliations._form',
                    ['bankReconciliation' => null]
                    )

                </form>

            </div>

        </div>
    </div>

</x-app-layout>
