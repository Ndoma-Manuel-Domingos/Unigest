<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Registar Universidade
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="rounded-lg bg-white p-6 shadow">
                <form method="POST" action="{{ route('platform.universities.store') }}">

                    @include('platform.universities.form')

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
