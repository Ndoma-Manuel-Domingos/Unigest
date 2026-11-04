<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">

            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Editar Conta Bancária
                </h2>

                <p class="text-sm text-gray-500 mt-1">
                    {{ $bankAccount->name }}
                </p>
            </div>

            <a href="{{ route('university.bank-accounts.show', $bankAccount) }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                Voltar
            </a>

        </div>
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

                <form method="POST" action="{{ route('university.bank-accounts.update', $bankAccount) }}">

                    @csrf
                    @method('PUT')

                    @include(
                    'university.finance.bank-accounts._form',
                    ['submitLabel' => 'Actualizar Conta']
                    )

                </form>

            </div>

        </div>

    </div>

</x-app-layout>
