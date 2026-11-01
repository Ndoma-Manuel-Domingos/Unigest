<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">

    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">

                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('university.dashboard') }}">

                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->

                {{-- MENU PRINCIPAL --}}
                <div class="hidden sm:flex sm:items-center sm:ms-10 sm:space-x-3">

                    @if(auth()->check() && auth()->user()->tenant_id)

                    {{-- PAINEL --}}
                    <x-nav-link :href="route('university.dashboard')" :active="request()->routeIs('university.dashboard')">
                        <i class="fas fa-home me-1"></i>
                        Painel
                    </x-nav-link>


                    {{-- ================================================= --}}
                    {{-- MÓDULO ACADÉMICO --}}
                    {{-- ================================================= --}}

                    @if(
                    auth()->user()->can('users.view') ||
                    auth()->user()->can('faculties.view') ||
                    auth()->user()->can('departments.view') ||
                    auth()->user()->can('courses.view') ||
                    auth()->user()->can('subjects.view') ||
                    auth()->user()->can('curricula.view') ||
                    auth()->user()->can('academic_years.view') ||
                    auth()->user()->can('classes.view') ||
                    auth()->user()->can('students.view') ||
                    auth()->user()->can('enrollments.view') ||
                    auth()->user()->can('rooms.view') ||
                    auth()->user()->can('teachers.view')
                    )

                    <x-dropdown align="left" width="72">

                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-gray-600 bg-white hover:text-gray-900 transition">
                                <i class="fas fa-graduation-cap me-2 text-blue-600"></i>
                                Académico
                                <i class="fas fa-chevron-down ms-2 text-xs"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">

                            @can('users.view')
                            <x-dropdown-link :href="route('university.users.index')">
                                <i class="fas fa-users me-2"></i>
                                Utilizadores
                            </x-dropdown-link>
                            @endcan

                            @can('faculties.view')
                            <x-dropdown-link :href="route('university.faculties.index')">
                                <i class="fas fa-university me-2"></i>
                                Faculdades
                            </x-dropdown-link>
                            @endcan

                            @can('departments.view')
                            <x-dropdown-link :href="route('university.departments.index')">
                                <i class="fas fa-sitemap me-2"></i>
                                Departamentos
                            </x-dropdown-link>
                            @endcan

                            @can('courses.view')
                            <x-dropdown-link :href="route('university.courses.index')">
                                <i class="fas fa-book me-2"></i>
                                Cursos
                            </x-dropdown-link>
                            @endcan

                            @can('subjects.view')
                            <x-dropdown-link :href="route('university.subjects.index')">
                                <i class="fas fa-book-open me-2"></i>
                                Disciplinas
                            </x-dropdown-link>
                            @endcan

                            @can('curricula.view')
                            <x-dropdown-link :href="route('university.curricula.index')">
                                <i class="fas fa-list-alt me-2"></i>
                                Planos Curriculares
                            </x-dropdown-link>
                            @endcan

                            @can('academic_years.view')
                            <x-dropdown-link :href="route('university.academic-years.index')">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Anos Lectivos
                            </x-dropdown-link>
                            @endcan

                            @can('classes.view')
                            <x-dropdown-link :href="route('university.classes.index')">
                                <i class="fas fa-chalkboard me-2"></i>
                                Turmas e Classes
                            </x-dropdown-link>
                            @endcan

                            @can('students.view')
                            <x-dropdown-link :href="route('university.students.index')">
                                <i class="fas fa-user-graduate me-2"></i>
                                Estudantes
                            </x-dropdown-link>
                            @endcan

                            @can('enrollments.view')
                            <x-dropdown-link :href="route('university.enrollments.index')">
                                <i class="fas fa-file-signature me-2"></i>
                                Matrículas
                            </x-dropdown-link>
                            @endcan

                            @can('subject_enrollments.view')
                            <x-dropdown-link :href="route('university.enrollments.index', ['enrollment' => request()->route('enrollment')])">
                                <i class="fas fa-clipboard-list me-2"></i>
                                Inscrições em Disciplinas
                            </x-dropdown-link>
                            @endcan

                            @can('rooms.view')
                            <x-dropdown-link :href="route('university.rooms.index')">
                                <i class="fas fa-door-open me-2"></i>
                                Salas
                            </x-dropdown-link>
                            @endcan

                            @can('teachers.view')
                            <x-dropdown-link :href="route('university.teachers.index')">
                                <i class="fas fa-chalkboard-teacher me-2"></i>
                                Professores
                            </x-dropdown-link>
                            @endcan

                        </x-slot>
                    </x-dropdown>

                    @endif


                    {{-- ================================================= --}}
                    {{-- MÓDULO FINANCEIRO --}}
                    {{-- ================================================= --}}

                    @if(
                    auth()->user()->can('finance.fee-concepts.view') ||
                    auth()->user()->can('finance.fee-tariffs.view') ||
                    auth()->user()->can('finance.invoices.view') ||
                    auth()->user()->can('finance.payments.view')
                    )

                    <x-dropdown align="left" width="72">

                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-gray-600 bg-white hover:text-gray-900 transition">
                                <i class="fas fa-wallet me-2 text-green-600"></i>
                                Financeiro
                                <i class="fas fa-chevron-down ms-2 text-xs"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">

                            @can('finance.fee-concepts.view')
                            <x-dropdown-link :href="route('university.fee-concepts.index')">
                                <i class="fas fa-tags me-2"></i>
                                Conceitos de Cobrança
                            </x-dropdown-link>
                            @endcan

                            @can('finance.fee-tariffs.view')
                            <x-dropdown-link :href="route('university.fee-tariffs.index')">
                                <i class="fas fa-money-bill-wave me-2"></i>
                                Tabelas de Preços
                            </x-dropdown-link>
                            @endcan

                            @can('finance.invoices.view')
                            <x-dropdown-link :href="route('university.invoices.index')">
                                <i class="fas fa-file-invoice me-2"></i>
                                Facturação
                            </x-dropdown-link>
                            @endcan

                            @can('finance.payments.view')
                            <x-dropdown-link :href="route('university.payments.index')">
                                <i class="fas fa-receipt me-2"></i>
                                Pagamentos e Recibos
                            </x-dropdown-link>
                            @endcan



                            @can('finance.suppliers.view')
                            <x-dropdown-link :href="route('university.suppliers.index')" :active="request()->routeIs('university.suppliers.*')">
                                <i class="fas fa-cash-register me-2"></i>
                                Fornecedores
                            </x-dropdown-link>
                            @endcan

                            @can('finance.expense-categories.view')
                            <x-dropdown-link :href="route('university.expense-categories.index')" :active="request()->routeIs('university.expense-categories.*')">
                                <i class="fas fa-cash-register me-2"></i>
                                Categorias de despesas
                            </x-dropdown-link>
                            @endcan

                            @can('finance.expenses.view')
                            <x-dropdown-link :href="route('university.expenses.index')" :active="request()->routeIs('university.expenses.*')">
                                <i class="fas fa-cash-register me-2"></i>
                                Despesas
                            </x-dropdown-link>
                            @endcan

                            @can('finance.reports.view')
                            <x-dropdown-link :href="route('university.financial-reports.index')" :active="request()->routeIs('university.financial-reports.*')">
                                <i class="fas fa-cash-register me-2"></i>
                                Relatórios financeiros
                            </x-dropdown-link>
                            @endcan

                        </x-slot>
                    </x-dropdown>

                    @endif


                    {{-- ================================================= --}}
                    {{-- TESOURARIA E CAIXA --}}
                    {{-- ================================================= --}}

                    @can('finance.cash.view')

                    <x-dropdown align="left" width="72">

                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-gray-600 bg-white hover:text-gray-900 transition">
                                <i class="fas fa-cash-register me-2 text-orange-600"></i>
                                Tesouraria e Caixa
                                <i class="fas fa-chevron-down ms-2 text-xs"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">

                            <x-dropdown-link :href="route('university.cash.index')">
                                <i class="fas fa-cash-register me-2"></i>
                                Caixa e Tesouraria
                            </x-dropdown-link>

                        </x-slot>
                    </x-dropdown>

                    @endcan


                    {{-- ================================================= --}}
                    {{-- ADMINISTRAÇÃO --}}
                    {{-- ================================================= --}}

                    <x-dropdown align="left" width="72">

                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-gray-600 bg-white hover:text-gray-900 transition">
                                <i class="fas fa-cog me-2 text-gray-600"></i>
                                Administração
                                <i class="fas fa-chevron-down ms-2 text-xs"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">

                            <x-dropdown-link :href="route('university.roles.index')">
                                <i class="fas fa-user-shield me-2"></i>
                                Perfis e Permissões
                            </x-dropdown-link>

                        </x-slot>

                    </x-dropdown>

                    @endif

                </div>


            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">

                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">

                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />

                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">

        <div class="pt-2 pb-3 space-y-1">

            @if(auth()->check() && auth()->user()->tenant_id)

            <x-responsive-nav-link :href="route('university.dashboard')" :active="request()->routeIs('university.dashboard')">
                Painel
            </x-responsive-nav-link>


            {{-- ACADÉMICO --}}
            <div class="px-4 pt-4 pb-2 text-xs font-bold uppercase tracking-wider text-blue-600">
                <i class="fas fa-graduation-cap me-2"></i>
                Módulo Académico
            </div>

            @can('users.view')
            <x-responsive-nav-link :href="route('university.users.index')">
                Utilizadores
            </x-responsive-nav-link>
            @endcan

            @can('faculties.view')
            <x-responsive-nav-link :href="route('university.faculties.index')">
                Faculdades
            </x-responsive-nav-link>
            @endcan

            @can('departments.view')
            <x-responsive-nav-link :href="route('university.departments.index')">
                Departamentos
            </x-responsive-nav-link>
            @endcan

            @can('courses.view')
            <x-responsive-nav-link :href="route('university.courses.index')">
                Cursos
            </x-responsive-nav-link>
            @endcan

            @can('subjects.view')
            <x-responsive-nav-link :href="route('university.subjects.index')">
                Disciplinas
            </x-responsive-nav-link>
            @endcan

            @can('curricula.view')
            <x-responsive-nav-link :href="route('university.curricula.index')">
                Planos Curriculares
            </x-responsive-nav-link>
            @endcan

            @can('academic_years.view')
            <x-responsive-nav-link :href="route('university.academic-years.index')">
                Anos Lectivos
            </x-responsive-nav-link>
            @endcan

            @can('classes.view')
            <x-responsive-nav-link :href="route('university.classes.index')">
                Turmas e Classes
            </x-responsive-nav-link>
            @endcan

            @can('students.view')
            <x-responsive-nav-link :href="route('university.students.index')">
                Estudantes
            </x-responsive-nav-link>
            @endcan

            @can('enrollments.view')
            <x-responsive-nav-link :href="route('university.enrollments.index')">
                Matrículas
            </x-responsive-nav-link>
            @endcan


            {{-- FINANCEIRO --}}
            <div class="px-4 pt-4 pb-2 text-xs font-bold uppercase tracking-wider text-green-600">
                <i class="fas fa-wallet me-2"></i>
                Módulo Financeiro
            </div>

            @can('finance.fee-concepts.view')
            <x-responsive-nav-link :href="route('university.fee-concepts.index')">
                Conceitos de Cobrança
            </x-responsive-nav-link>
            @endcan

            @can('finance.fee-tariffs.view')
            <x-responsive-nav-link :href="route('university.fee-tariffs.index')">
                Tabelas de Preços
            </x-responsive-nav-link>
            @endcan

            @can('finance.invoices.view')
            <x-responsive-nav-link :href="route('university.invoices.index')">
                Facturação
            </x-responsive-nav-link>
            @endcan

            @can('finance.payments.view')
            <x-responsive-nav-link :href="route('university.payments.index')">
                Pagamentos e Recibos
            </x-responsive-nav-link>
            @endcan


            {{-- TESOURARIA --}}
            @can('finance.cash.view')

            <div class="px-4 pt-4 pb-2 text-xs font-bold uppercase tracking-wider text-orange-600">
                <i class="fas fa-cash-register me-2"></i>
                Tesouraria e Caixa
            </div>

            <x-responsive-nav-link :href="route('university.cash.index')">
                Caixa e Tesouraria
            </x-responsive-nav-link>

            @endcan


            {{-- ADMINISTRAÇÃO --}}
            <div class="px-4 pt-4 pb-2 text-xs font-bold uppercase tracking-wider text-gray-500">
                <i class="fas fa-cog me-2"></i>
                Administração
            </div>

            <x-responsive-nav-link :href="route('university.roles.index')">
                Perfis e Permissões
            </x-responsive-nav-link>

            @endif

        </div>


        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>
            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
