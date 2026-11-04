<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">
                Operações da Tesouraria
            </h2>
            <p class="text-sm text-gray-500">
                Área central de trabalho do tesoureiro.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- INDICADORES --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div class="bg-white border rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Caixas abertas</p>
                    <p class="text-3xl font-bold text-blue-700 mt-2">
                        {{ $openCashCount }}
                    </p>
                    <a href="{{ route('university.cash.index') }}" class="text-sm text-indigo-600 hover:underline mt-3 inline-block">
                        Consultar caixas →
                    </a>
                </div>

                <div class="bg-white border rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Facturas por liquidar</p>
                    <p class="text-3xl font-bold text-orange-600 mt-2">
                        {{ $pendingInvoicesCount }}
                    </p>
                    <a href="{{ route('university.invoices.index') }}" class="text-sm text-indigo-600 hover:underline mt-3 inline-block">
                        Consultar facturas →
                    </a>
                </div>

                <div class="bg-white border rounded-xl p-5 shadow-sm">
                    <p class="text-sm text-gray-500">Despesas por pagar</p>
                    <p class="text-3xl font-bold text-red-600 mt-2">
                        {{ $pendingExpensesCount }}
                    </p>
                    <a href="{{ route('university.expenses.index') }}" class="text-sm text-indigo-600 hover:underline mt-3 inline-block">
                        Consultar despesas →
                    </a>
                </div>
            </div>

            {{-- RECEBIMENTOS --}}
            <section class="bg-white border rounded-xl shadow-sm p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Recebimentos
                    </h3>
                    <p class="text-sm text-gray-500">
                        Cobrança de propinas, matrículas, inscrições e serviços.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @can('finance.invoices.view')
                    <a href="{{ route('university.invoices.index') }}" class="border rounded-lg p-5 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <h4 class="font-semibold text-gray-800">
                            Facturas de estudantes
                        </h4>
                        <p class="text-sm text-gray-500 mt-2">
                            Consultar facturas, saldos e pagamentos pendentes.
                        </p>
                        <span class="text-sm text-indigo-600 mt-3 inline-block">
                            Abrir facturas →
                        </span>
                    </a>
                    @endcan

                    @can('finance.cash.view')
                    <a href="{{ route('university.cash.index') }}" class="border rounded-lg p-5 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <h4 class="font-semibold text-gray-800">
                            Caixas e recebimentos
                        </h4>
                        <p class="text-sm text-gray-500 mt-2">
                            Consultar caixas, operadores e movimentos registados.
                        </p>
                        <span class="text-sm text-indigo-600 mt-3 inline-block">
                            Abrir caixas →
                        </span>
                    </a>
                    @endcan
                </div>
            </section>

            {{-- PAGAMENTOS --}}
            <section class="bg-white border rounded-xl shadow-sm p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Pagamentos
                    </h3>
                    <p class="text-sm text-gray-500">
                        Gestão das obrigações financeiras da universidade.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @can('finance.expenses.view')
                    <a href="{{ route('university.expenses.index') }}" class="border rounded-lg p-5 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <h4 class="font-semibold text-gray-800">
                            Despesas e fornecedores
                        </h4>
                        <p class="text-sm text-gray-500 mt-2">
                            Consultar despesas aprovadas, pagamentos e saldos.
                        </p>
                        <span class="text-sm text-indigo-600 mt-3 inline-block">
                            Gerir despesas →
                        </span>
                    </a>
                    @endcan

                    @can('finance.cash.view')
                    <a href="{{ route('university.cash.index') }}" class="border rounded-lg p-5 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <h4 class="font-semibold text-gray-800">
                            Movimentos de caixa
                        </h4>
                        <p class="text-sm text-gray-500 mt-2">
                            Consultar entradas, saídas e fechos de caixa.
                        </p>
                        <span class="text-sm text-indigo-600 mt-3 inline-block">
                            Consultar caixa →
                        </span>
                    </a>
                    @endcan
                </div>
            </section>

            {{-- BANCOS E CONTROLO --}}
            <section class="bg-white border rounded-xl shadow-sm p-6">
                <div class="mb-5">
                    <h3 class="text-lg font-semibold text-gray-800">
                        Bancos e controlo financeiro
                    </h3>
                    <p class="text-sm text-gray-500">
                        Gestão bancária e acompanhamento dos movimentos.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @can('finance.bank-accounts.view')
                    <a href="{{ route('university.bank-accounts.index') }}" class="border rounded-lg p-5 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <h4 class="font-semibold text-gray-800">Contas bancárias</h4>
                        <p class="text-sm text-gray-500 mt-2">
                            Consultar contas e saldos bancários.
                        </p>
                    </a>
                    @endcan

                    @can('finance.bank-movements.view')
                    <a href="{{ route('university.bank-movements.index') }}" class="border rounded-lg p-5 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <h4 class="font-semibold text-gray-800">Movimentos bancários</h4>
                        <p class="text-sm text-gray-500 mt-2">
                            Consultar entradas, saídas e transferências.
                        </p>
                    </a>
                    @endcan

                    @can('finance.bank-reconciliations.view')
                    <a href="{{ route('university.bank-reconciliations.index') }}" class="border rounded-lg p-5 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <h4 class="font-semibold text-gray-800">Reconciliação bancária</h4>
                        <p class="text-sm text-gray-500 mt-2">
                            Conferir extractos e movimentos bancários.
                        </p>
                    </a>
                    @endcan

                    @can('finance.treasury.movements.view')
                    <a href="{{ route('university.treasury.movements.index') }}" class="border rounded-lg p-5 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <h4 class="font-semibold text-gray-800">Livro de movimentos</h4>
                        <p class="text-sm text-gray-500 mt-2">
                            Consultar os movimentos financeiros centralizados.
                        </p>
                    </a>
                    @endcan

                    @can('finance.bank-reports.view')
                    <a href="{{ route('university.bank-reports.index') }}" class="border rounded-lg p-5 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <h4 class="font-semibold text-gray-800">Relatórios bancários</h4>
                        <p class="text-sm text-gray-500 mt-2">
                            Consultar os mapas e indicadores bancários.
                        </p>
                    </a>
                    @endcan

                    @can('finance.audit.view')
                    <a href="{{ route('university.financial-audit.index') }}" class="border rounded-lg p-5 hover:border-indigo-400 hover:bg-indigo-50 transition">
                        <h4 class="font-semibold text-gray-800">Auditoria financeira</h4>
                        <p class="text-sm text-gray-500 mt-2">
                            Consultar o histórico das operações financeiras.
                        </p>
                    </a>
                    @endcan
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
