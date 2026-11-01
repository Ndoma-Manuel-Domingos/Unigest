<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Registar Utilizador
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-xl bg-white p-6 shadow">
                <form method="POST" action="{{ route('university.users.store') }}">
                    @include('university.users.form', ['user' => null])
                </form>
            </div>
        </div>
    </div>

</x-app-layout>
