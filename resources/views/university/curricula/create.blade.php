<x-app-layout>

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-800">
                    Novo Currículo
                </h2>

                <p class="text-sm text-gray-500">
                    Criar um novo plano curricular.
                </p>
            </div>

            <a href="{{ route('university.curricula.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">

            @if($errors->any())
            <div class="mb-6 rounded-md bg-red-50 p-4 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="rounded-lg bg-white p-6 shadow">
                <form method="POST" action="{{ route('university.curricula.store') }}">
                    @include('university.curricula.form')
                </form>
            </div>

        </div>
    </div>

</x-app-layout>
