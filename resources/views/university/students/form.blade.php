@csrf

@if(isset($student))
@method('PUT')
@endif

@if($errors->any())
<div class="mb-5 bg-red-100 text-red-800 p-4 rounded-lg">
    <ul class="list-disc pl-5">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="space-y-8">

    {{-- IDENTIFICAÇÃO --}}
    <section>
        <h3 class="text-lg font-semibold border-b pb-2 mb-4">
            1. Identificação do Estudante
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            @if(isset($student))
            <div>
                <label class="block font-medium mb-1">Número de Estudante</label>
                <input type="text" value="{{ $student->student_number }}" disabled class="w-full rounded-lg bg-gray-100 border-gray-300">
            </div>
            @endif

            <div class="md:col-span-2">
                <label class="block font-medium mb-1">Nome Completo *</label>
                <input type="text" name="name" value="{{ old('name', $student->name ?? '') }}" required class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">NIF</label>
                <input type="text" name="nif" value="{{ old('nif', $student->nif ?? '') }}" class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">Tipo de Documento</label>
                <select name="document_type" class="w-full rounded-lg border-gray-300">
                    <option value="">Seleccione</option>
                    @foreach(['BI' => 'Bilhete de Identidade', 'Passaporte' => 'Passaporte', 'Cartão de Residência' => 'Cartão de Residência', 'Outro' => 'Outro'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('document_type', $student->document_type ?? '') === $value)>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Número do Documento</label>
                <input type="text" name="document_number" value="{{ old('document_number', $student->document_number ?? '') }}" class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">Género</label>
                <select name="gender" class="w-full rounded-lg border-gray-300">
                    <option value="">Seleccione</option>
                    <option value="masculino" @selected(old('gender', $student->gender ?? '') === 'masculino')>Masculino</option>
                    <option value="feminino" @selected(old('gender', $student->gender ?? '') === 'feminino')>Feminino</option>
                    <option value="outro" @selected(old('gender', $student->gender ?? '') === 'outro')>Outro</option>
                </select>
            </div>

            <div>
                <label class="block font-medium mb-1">Data de Nascimento</label>
                <input type="date" name="birth_date" max="{{ now()->subDay()->format('Y-m-d') }}" value="{{ old('birth_date', isset($student) && $student->birth_date ? $student->birth_date->format('Y-m-d') : '') }}" class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">Local de Nascimento</label>
                <input type="text" name="birth_place" value="{{ old('birth_place', $student->birth_place ?? '') }}" class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">Nacionalidade</label>
                <input type="text" name="nationality" value="{{ old('nationality', $student->nationality ?? 'Angolana') }}" class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">Estado Civil</label>
                <select name="marital_status" class="w-full rounded-lg border-gray-300">
                    <option value="">Seleccione</option>
                    @foreach(['solteiro' => 'Solteiro(a)', 'casado' => 'Casado(a)', 'divorciado' => 'Divorciado(a)', 'viuvo' => 'Viúvo(a)', 'outro' => 'Outro'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('marital_status', $student->marital_status ?? '') === $value)>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </section>

    {{-- CONTACTOS --}}
    <section>
        <h3 class="text-lg font-semibold border-b pb-2 mb-4">
            2. Contactos e Residência
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block font-medium mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $student->email ?? '') }}" class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">Telefone Principal</label>
                <input type="text" name="phone" value="{{ old('phone', $student->phone ?? '') }}" class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">Telefone Alternativo</label>
                <input type="text" name="alternative_phone" value="{{ old('alternative_phone', $student->alternative_phone ?? '') }}" class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">Província</label>
                <input type="text" name="province" value="{{ old('province', $student->province ?? '') }}" class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">Município</label>
                <input type="text" name="municipality" value="{{ old('municipality', $student->municipality ?? '') }}" class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">Morada</label>
                <input type="text" name="address" value="{{ old('address', $student->address ?? '') }}" class="w-full rounded-lg border-gray-300">
            </div>
        </div>
    </section>

    {{-- ENCARREGADO --}}
    <section>
        <h3 class="text-lg font-semibold border-b pb-2 mb-4">
            3. Encarregado de Educação
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
                <label class="block font-medium mb-1">Nome do Encarregado</label>
                <input type="text" name="guardian_name" value="{{ old('guardian_name', $student->guardian_name ?? '') }}" class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">Telefone do Encarregado</label>
                <input type="text" name="guardian_phone" value="{{ old('guardian_phone', $student->guardian_phone ?? '') }}" class="w-full rounded-lg border-gray-300">
            </div>

            <div>
                <label class="block font-medium mb-1">Parentesco</label>
                <input type="text" name="guardian_relationship" value="{{ old('guardian_relationship', $student->guardian_relationship ?? '') }}" placeholder="Ex.: Pai, Mãe, Tio..." class="w-full rounded-lg border-gray-300">
            </div>
        </div>
    </section>

    {{-- FOTOGRAFIA --}}
    <section>
        <h3 class="text-lg font-semibold border-b pb-2 mb-4">
            4. Fotografia
        </h3>

        @if(isset($student) && $student->photo)
        <img src="{{ Storage::disk('public')->url($student->photo) }}" class="w-28 h-28 rounded-lg object-cover mb-3">
        @endif

        <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="w-full rounded-lg border border-gray-300 p-2">

        <p class="text-sm text-gray-500 mt-1">
            Formatos JPG, PNG ou WEBP. Tamanho máximo: 2 MB.
        </p>
    </section>

    {{-- OBSERVAÇÕES --}}
    <section>
        <h3 class="text-lg font-semibold border-b pb-2 mb-4">
            5. Observações
        </h3>

        <textarea name="observations" rows="4" class="w-full rounded-lg border-gray-300">{{ old('observations', $student->observations ?? '') }}</textarea>
    </section>

    {{-- ESTADO --}}
    @if(isset($student))
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $student->is_active))
        class="rounded border-gray-300">

        <label>Estudante activo</label>
    </div>
    @endif

</div>

<div class="flex justify-end gap-3 mt-8 border-t pt-5">
    <a href="{{ route('university.students.index') }}" class="bg-gray-200 px-5 py-2 rounded-lg">
        Cancelar
    </a>

    <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
        Guardar Estudante
    </button>
</div>
