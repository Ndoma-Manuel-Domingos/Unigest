<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Criar tipo de bolsa
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow">
                <form method="POST" action="{{ route('university.scholarship-types.store') }}">
                    @include('university.scholarships.types._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
