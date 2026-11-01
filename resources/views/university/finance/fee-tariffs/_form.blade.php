@php
$selectedFrequency = old('frequency', $feeTariff->frequency ?? 'once');
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">

    <div>
        <label for="fee_concept_id" class="mb-2 block text-sm font-semibold text-gray-700">
            Conceito de taxa
        </label>

        <select name="fee_concept_id" id="fee_concept_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Seleccionar conceito</option>

            @foreach($concepts as $concept)
            <option value="{{ $concept->id }}" @selected(old('fee_concept_id', $feeTariff->fee_concept_id ?? '') == $concept->id)>
                {{ $concept->name }} ({{ $concept->code }})
            </option>
            @endforeach
        </select>

        @error('fee_concept_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="academic_year_id" class="mb-2 block text-sm font-semibold text-gray-700">
            Ano académico
        </label>

        <select name="academic_year_id" id="academic_year_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Seleccionar ano académico</option>

            @foreach($academicYears as $year)
            <option value="{{ $year->id }}" @selected(old('academic_year_id', $feeTariff->academic_year_id ?? '') == $year->id)>
                {{ $year->name }}
            </option>
            @endforeach
        </select>

        @error('academic_year_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="course_id" class="mb-2 block text-sm font-semibold text-gray-700">
            Curso
        </label>

        <select name="course_id" id="course_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">Todos os cursos</option>

            @foreach($courses as $course)
            <option value="{{ $course->id }}" @selected(old('course_id', $feeTariff->course_id ?? '') == $course->id)>
                {{ $course->name }}
            </option>
            @endforeach
        </select>

        @error('course_id')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="amount" class="mb-2 block text-sm font-semibold text-gray-700">
            Valor
        </label>

        <input type="number" name="amount" id="amount" value="{{ old('amount', $feeTariff->amount ?? '') }}" min="0.01" step="0.01" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0,00" required>

        @error('amount')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="frequency" class="mb-2 block text-sm font-semibold text-gray-700">
            Frequência de cobrança
        </label>

        <select name="frequency" id="frequency" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="once" @selected($selectedFrequency==='once' )>Única</option>
            <option value="monthly" @selected($selectedFrequency==='monthly' )>Mensal</option>
            <option value="semesterly" @selected($selectedFrequency==='semesterly' )>Semestral</option>
            <option value="annually" @selected($selectedFrequency==='annually' )>Anual</option>
        </select>

        @error('frequency')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div id="due-day-container">
        <label for="due_day" class="mb-2 block text-sm font-semibold text-gray-700">
            Dia de vencimento
        </label>

        <input type="number" name="due_day" id="due_day" value="{{ old('due_day', $feeTariff->due_day ?? '') }}" min="1" max="28" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ex.: 10">

        @error('due_day')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror

        <p class="mt-1 text-xs text-gray-500">
            Obrigatório para tarifas mensais.
        </p>
    </div>

    <div>
        <label for="currency" class="mb-2 block text-sm font-semibold text-gray-700">
            Moeda
        </label>

        <select name="currency" id="currency" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="AOA" @selected(old('currency', $feeTariff->currency ?? 'AOA') === 'AOA')>
                Kwanza (AOA)
            </option>
            <option value="USD" @selected(old('currency', $feeTariff->currency ?? '') === 'USD')>
                Dólar (USD)
            </option>
            <option value="EUR" @selected(old('currency', $feeTariff->currency ?? '') === 'EUR')>
                Euro (EUR)
            </option>
        </select>

        @error('currency')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2">
        <label for="description" class="mb-2 block text-sm font-semibold text-gray-700">
            Descrição
        </label>

        <textarea name="description" id="description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Descrição da tarifa">{{ old('description', $feeTariff->description ?? '') }}</textarea>

        @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="md:col-span-2 rounded-lg border border-gray-200 p-4">
        <label class="flex items-center gap-3">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_active', $feeTariff->is_active ?? true))>

            <span>
                <span class="block text-sm font-semibold text-gray-800">
                    Tarifa activa
                </span>
                <span class="block text-xs text-gray-500">
                    Disponibiliza esta tarifa para utilização na facturação.
                </span>
            </span>
        </label>

        @error('is_active')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const frequency = document.getElementById('frequency');
        const dueDayContainer = document.getElementById('due-day-container');
        const dueDay = document.getElementById('due_day');

        function updateDueDay() {
            const isMonthly = frequency.value === 'monthly';

            dueDayContainer.classList.toggle('opacity-50', !isMonthly);
            dueDay.disabled = !isMonthly;
            dueDay.required = isMonthly;

            if (!isMonthly) {
                dueDay.value = '';
            }
        }

        frequency.addEventListener('change', updateDueDay);
        updateDueDay();
    });

</script>
