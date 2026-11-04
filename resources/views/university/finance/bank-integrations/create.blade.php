<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Nova integração bancária
                </h2>
                <p class="text-sm text-gray-500">
                    Configurar a ligação bancária da universidade.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-5 p-4 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            @if($accounts->isEmpty())
            <div class="p-5 bg-yellow-50 text-yellow-800 rounded-lg">
                Não existem contas bancárias activas.
                Primeiro registe uma conta bancária.
            </div>
            @else
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form method="POST" action="{{ route('university.bank-integrations.store') }}">
                    @include('university.finance.bank-integrations._form')
                </form>
            </div>
            @endif

        </div>
    </div>

</x-app-layout>
