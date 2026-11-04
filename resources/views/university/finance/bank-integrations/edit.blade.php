<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Editar integração bancária
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-sm rounded-lg p-6">

                <form method="POST" action="{{ route('university.bank-integrations.update', $integration) }}">

                    @method('PUT')

                    @include('university.finance.bank-integrations._form', [
                    'integration' => $integration
                    ])

                </form>

            </div>

        </div>
    </div>

</x-app-layout>
