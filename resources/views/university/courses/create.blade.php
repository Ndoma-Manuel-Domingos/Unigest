<x-app-layout>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Novo Curso
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 shadow-sm rounded-lg">

                <form method="POST" action="{{ route('university.courses.store') }}">

                    @include('university.courses.form')

                </form>

            </div>

        </div>
    </div>

</x-app-layout>
