<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Nova Conta Bancária
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6">

                @if($errors->any())
                <div class="mb-6 bg-red-100 border border-red-300 text-red-800 px-4 py-3 rounded-lg">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('university.bank-accounts.store') }}">

                    @csrf

                    @include(
                    'university.finance.bank-accounts._form',
                    ['submitLabel' => 'Criar Conta']
                    )

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
