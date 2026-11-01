<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Registar despesa</h2>
            <p class="text-sm text-gray-500">A despesa será criada em estado de rascunho.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('university.expenses.store') }}">
                    @csrf
                    @include('university.finance.expenses._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
