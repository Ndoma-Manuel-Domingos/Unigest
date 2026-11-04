<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Importar Extrato Bancário
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $bankReconciliation->reference }}
                    —
                    {{ $bankReconciliation->bankAccount->name }}
                </p>
            </div>

            <a href="{{ route('university.bank-reconciliations.show', $bankReconciliation) }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                Voltar
            </a>

        </div>
    </x-slot>

    <div class="py-6">

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            @if($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">

                <ul class="list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
            @endif

            <div class="bg-white rounded-xl shadow-sm border border-gray-200">

                <div class="p-6 border-b border-gray-200">

                    <h3 class="text-lg font-semibold text-gray-800">
                        Importação CSV
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        O ficheiro deve conter as colunas:
                    </p>

                    <div class="mt-3 flex flex-wrap gap-2">

                        @foreach(['date', 'description', 'reference', 'type', 'amount'] as $column)

                        <span class="px-2.5 py-1 rounded-md bg-gray-100 text-xs font-mono text-gray-700">
                            {{ $column }}
                        </span>

                        @endforeach

                    </div>

                </div>

                <form method="POST" action="{{ route('university.bank-reconciliations.import.store', $bankReconciliation) }}" enctype="multipart/form-data" class="p-6">

                    @csrf

                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700">
                            Ficheiro CSV
                        </label>

                        <input type="file" name="file" id="file" accept=".csv,.txt" required class="mt-2 block w-full rounded-lg border-gray-300">

                        <p class="mt-2 text-xs text-gray-500">
                            Tamanho máximo: 5 MB.
                        </p>
                    </div>

                    <div class="mt-6 rounded-lg bg-blue-50 border border-blue-200 p-4">

                        <p class="text-sm font-semibold text-blue-800">
                            Exemplo
                        </p>

                        <pre class="mt-2 text-xs text-blue-900 overflow-x-auto">date,description,reference,type,amount
2026-10-01,Pagamento de propina,PGT-001,income,150000
2026-10-02,Pagamento fornecedor,FOR-001,expense,45000</pre>

                    </div>

                    <div class="mt-6 flex justify-end gap-3">

                        <a href="{{ route('university.bank-reconciliations.show', $bankReconciliation) }}" class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200">
                            Cancelar
                        </a>

                        <button type="submit" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700">
                            Importar Extrato
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
