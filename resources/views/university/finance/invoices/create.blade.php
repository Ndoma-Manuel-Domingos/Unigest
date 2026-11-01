<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Nova Factura</h2>
            <p class="mt-1 text-sm text-gray-500">
                Criar uma factura para um estudante.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl space-y-6 px-4 sm:px-6 lg:px-8">

            @if($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="mb-2 font-semibold">Verifica os seguintes campos:</p>
                <ul class="list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form method="POST" action="{{ route('university.invoices.store') }}" id="invoice-form">
                @csrf

                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="font-semibold text-gray-800">Dados da factura</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Selecciona o estudante e define as datas de cobrança.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">

                        <div>
                            <label for="student_id" class="mb-2 block text-sm font-semibold text-gray-700">
                                Estudante
                            </label>

                            <select name="student_id" id="student_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                <option value="">Seleccionar estudante</option>
                                @foreach($students as $student)
                                <option value="{{ $student->id }}" @selected(old('student_id')==$student->id)>
                                    {{ $student->name ?? $student->full_name }}
                                    — {{ $student->student_number }}
                                </option>
                                @endforeach
                            </select>

                            @error('student_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="enrollment_id" class="mb-2 block text-sm font-semibold text-gray-700">
                                Matrícula
                            </label>

                            <select name="enrollment_id" id="enrollment_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Sem matrícula associada</option>

                                @foreach($enrollments as $enrollment)
                                <option value="{{ $enrollment->id }}" data-student="{{ $enrollment->student_id }}" data-year="{{ $enrollment->academic_year_id }}" data-course="{{ $enrollment->course_id }}" @selected(old('enrollment_id')==$enrollment->id)>
                                    {{ $enrollment->student->name ?? $enrollment->student->full_name }}
                                    — {{ $enrollment->course->name ?? 'Curso' }}
                                    — {{ $enrollment->academicYear->name ?? 'Ano académico' }}
                                </option>
                                @endforeach
                            </select>

                            @error('enrollment_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="issue_date" class="mb-2 block text-sm font-semibold text-gray-700">
                                Data de emissão
                            </label>

                            <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', now()->format('Y-m-d')) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>

                            @error('issue_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="due_date" class="mb-2 block text-sm font-semibold text-gray-700">
                                Data de vencimento
                            </label>

                            <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                            @error('due_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="font-semibold text-gray-800">Itens da factura</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Selecciona as tarifas que serão cobradas.
                        </p>
                    </div>

                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Seleccionar</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Conceito</th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase text-gray-500">Frequência</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Valor unitário</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold uppercase text-gray-500">Quantidade</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase text-gray-500">Subtotal</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100">
                                    @foreach($tariffs as $tariff)
                                    <tr class="tariff-row" data-year="{{ $tariff->academic_year_id }}" data-course="{{ $tariff->course_id ?? '' }}">
                                        <td class="px-4 py-4">
                                            <input type="checkbox" name="items[{{ $tariff->id }}][fee_tariff_id]" value="{{ $tariff->id }}" class="tariff-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" @checked(old('items.' . $tariff->id . '.fee_tariff_id'))>
                                        </td>

                                        <td class="px-4 py-4">
                                            <div class="font-semibold text-gray-800">
                                                {{ $tariff->feeConcept->name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $tariff->feeConcept->code }}
                                            </div>
                                        </td>

                                        <td class="px-4 py-4 text-sm text-gray-600">
                                            @switch($tariff->frequency)
                                            @case('once') Única @break
                                            @case('monthly') Mensal @break
                                            @case('semesterly') Semestral @break
                                            @case('annually') Anual @break
                                            @default {{ $tariff->frequency }}
                                            @endswitch
                                        </td>

                                        <td class="px-4 py-4 text-right">
                                            <span class="unit-price font-semibold text-gray-800">
                                                {{ number_format($tariff->amount, 2, '.', '') }}
                                            </span>
                                            <span class="text-xs text-gray-500">{{ $tariff->currency }}</span>
                                        </td>

                                        <td class="px-4 py-4 text-center">
                                            <input type="number" name="items[{{ $tariff->id }}][quantity]" value="{{ old('items.' . $tariff->id . '.quantity', 1) }}" min="1" step="1" class="quantity-input w-20 rounded-lg border-gray-300 text-center text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500" disabled>
                                        </td>

                                        <td class="px-4 py-4 text-right font-semibold text-gray-800">
                                            <span class="line-total">0,00</span>
                                            <span class="text-xs text-gray-500">{{ $tariff->currency }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @error('items')
                        <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="mb-4 font-semibold text-gray-800">Observações</h3>

                        <textarea name="notes" rows="5" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Observações da factura...">{{ old('notes') }}</textarea>

                        @error('notes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="mb-4 font-semibold text-gray-800">Resumo financeiro</h3>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500">Subtotal</span>
                                <strong id="subtotal-display">0,00</strong>
                            </div>

                            <div>
                                <label for="discount" class="mb-1 block text-sm font-medium text-gray-700">
                                    Desconto
                                </label>
                                <input type="number" name="discount" id="discount" min="0" step="0.01" value="{{ old('discount', 0) }}" class="w-full rounded-lg border-gray-300 text-right shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('discount')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="penalty" class="mb-1 block text-sm font-medium text-gray-700">
                                    Multa
                                </label>
                                <input type="number" name="penalty" id="penalty" min="0" step="0.01" value="{{ old('penalty', 0) }}" class="w-full rounded-lg border-gray-300 text-right shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('penalty')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                                <span class="font-semibold text-gray-800">Total</span>
                                <span id="total-display" class="text-xl font-bold text-indigo-700">
                                    0,00
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <a href="{{ route('university.invoices.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-100">
                        Cancelar
                    </a>

                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                        <i class="fas fa-save"></i>
                        Guardar factura como rascunho
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const studentSelect = document.getElementById('student_id');
            const enrollmentSelect = document.getElementById('enrollment_id');
            const rows = document.querySelectorAll('.tariff-row');
            const discountInput = document.getElementById('discount');
            const penaltyInput = document.getElementById('penalty');

            function formatMoney(value) {
                return value.toLocaleString('pt-PT', {
                    minimumFractionDigits: 2
                    , maximumFractionDigits: 2
                });
            }

            function filterEnrollments() {
                const studentId = studentSelect.value;

                Array.from(enrollmentSelect.options).forEach(option => {
                    if (!option.value) return;

                    const visible = !studentId || option.dataset.student === studentId;
                    option.hidden = !visible;

                    if (!visible && option.selected) {
                        enrollmentSelect.value = '';
                    }
                });
            }

            function filterTariffs() {
                const enrollment = enrollmentSelect.selectedOptions[0];
                const yearId = enrollment ? enrollment.dataset.year : '';
                const courseId = enrollment ? enrollment.dataset.course : '';

                rows.forEach(row => {
                    const sameYear = !yearId || row.dataset.year === yearId;
                    const sameCourse = !row.dataset.course ||
                        !courseId ||
                        row.dataset.course === courseId;

                    const visible = sameYear && sameCourse;
                    row.hidden = !visible;

                    if (!visible) {
                        const checkbox = row.querySelector('.tariff-checkbox');
                        const quantity = row.querySelector('.quantity-input');

                        checkbox.checked = false;
                        quantity.disabled = true;
                    }
                });

                calculateTotals();
            }

            function calculateTotals() {
                let subtotal = 0;

                rows.forEach(row => {
                    const checkbox = row.querySelector('.tariff-checkbox');
                    const quantity = row.querySelector('.quantity-input');
                    const price = parseFloat(row.querySelector('.unit-price').textContent) || 0;

                    quantity.disabled = !checkbox.checked;

                    const lineTotal = checkbox.checked ?
                        price * (parseInt(quantity.value, 10) || 1) :
                        0;

                    row.querySelector('.line-total').textContent = formatMoney(lineTotal);
                    subtotal += lineTotal;
                });

                const discount = parseFloat(discountInput.value) || 0;
                const penalty = parseFloat(penaltyInput.value) || 0;
                const total = Math.max(0, subtotal - discount) + penalty;

                document.getElementById('subtotal-display').textContent = formatMoney(subtotal);
                document.getElementById('total-display').textContent = formatMoney(total);
            }

            studentSelect.addEventListener('change', function() {
                filterEnrollments();
                filterTariffs();
            });

            enrollmentSelect.addEventListener('change', filterTariffs);
            discountInput.addEventListener('input', calculateTotals);
            penaltyInput.addEventListener('input', calculateTotals);

            rows.forEach(row => {
                row.querySelector('.tariff-checkbox').addEventListener('change', calculateTotals);
                row.querySelector('.quantity-input').addEventListener('input', calculateTotals);
            });

            filterEnrollments();
            filterTariffs();
            calculateTotals();
        });

    </script>
</x-app-layout>
