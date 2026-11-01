<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Editar Universidade
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="mb-4 rounded-lg bg-white p-4 shadow">
                <p class="text-sm text-gray-500">Código da universidade</p>
                <p class="font-semibold text-gray-800">
                    {{ $university->uuid }}
                </p>

                <p class="mt-2 text-sm text-gray-500">Estado actual</p>
                <p class="font-semibold text-gray-800">
                    {{ $university->status }}
                </p>
            </div>

            <div class="rounded-lg bg-white p-6 shadow">
                <form method="POST" action="{{ route('platform.universities.update', $university) }}">

                    @method('PUT')

                    @include('platform.universities.form')

                </form>
            </div>

        </div>
    </div>
</x-app-layout>
