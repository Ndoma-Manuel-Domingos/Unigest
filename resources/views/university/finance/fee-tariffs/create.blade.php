<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-800">
                Nova Tarifa
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Configurar uma nova tarifa académica.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            @if($errors->any())
            <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="mb-2 font-semibold">Verifica os seguintes campos:</p>
                <ul class="list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h3 class="font-semibold text-gray-800">
                        Dados da tarifa
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Define o conceito, o enquadramento académico e o valor.
                    </p>
                </div>

                <form method="POST" action="{{ route('university.fee-tariffs.store') }}">
                    @csrf

                    <div class="p-6">
                        @include('university.finance.fee-tariffs._form')
                    </div>

                    <div class="flex flex-col-reverse gap-3 border-t border-gray-100 bg-gray-50 px-6 py-4 sm:flex-row sm:justify-end">
                        <a href="{{ route('university.fee-tariffs.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                            Cancelar
                        </a>

                        <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">
                            <i class="fas fa-save"></i>
                            Guardar tarifa
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
