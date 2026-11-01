<div class="rounded-xl bg-white p-6 shadow">

    <h3 class="mb-4 text-lg font-semibold text-gray-800">
        Administração do Sistema
    </h3>

    <div class="flex flex-wrap gap-3">

        <a href="{{ route('university.users.index') }}" class="rounded-lg bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">
            <i class="fas fa-users mr-2"></i>
            Gestão de Utilizadores
        </a>

        <a href="{{ route('university.roles.index') }}" class="rounded-lg bg-purple-600 px-5 py-3 text-sm font-semibold text-white hover:bg-purple-700">
            <i class="fas fa-user-shield mr-2"></i>
            Perfis e Permissões
        </a>

    </div>

</div>
