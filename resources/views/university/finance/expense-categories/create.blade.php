<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-800">Nova categoria</h2>
            <p class="text-sm text-gray-500">Registar uma categoria de despesas.</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <form method="POST" action="{{ route('university.expense-categories.store') }}">
                    @csrf
                    @include('university.finance.expense-categories._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
