<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Registar Ano Lectivo
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <form method="POST" action="{{ route('university.academic-years.store') }}">
                    @include('university.academic-years.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
