<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Editar Turma: {{ $academicClass->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <form method="POST" action="{{ route('university.classes.update', $academicClass) }}">
                    @include('university.classes.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
