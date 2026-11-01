@csrf

@if(isset($curriculum))
@method('PUT')
@endif

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">

    <div>
        <x-input-label for="course_id" value="Curso" />

        <select id="course_id" name="course_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
            <option value="">Seleccione o curso</option>

            @foreach($courses as $course)
            <option value="{{ $course->id }}" @selected(old('course_id', $curriculum->course_id ?? '') == $course->id)
                >
                {{ $course->code }} — {{ $course->name }}
            </option>
            @endforeach
        </select>

        <x-input-error :messages="$errors->get('course_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="name" value="Nome do currículo" />

        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ old('name', $curriculum->name ?? '') }}" placeholder="Ex.: Plano Curricular 2026" required />

        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="version" value="Versão" />

        <x-text-input id="version" name="version" type="text" class="mt-1 block w-full" value="{{ old('version', $curriculum->version ?? '') }}" placeholder="Ex.: 1.0" />

        <x-input-error :messages="$errors->get('version')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="effective_from" value="Data de início" />

        <x-text-input id="effective_from" name="effective_from" type="date" class="mt-1 block w-full" value="{{ old(
                'effective_from',
                isset($curriculum->effective_from)
                    ? $curriculum->effective_from->format('Y-m-d')
                    : ''
            ) }}" />

        <x-input-error :messages="$errors->get('effective_from')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="effective_to" value="Data de fim" />

        <x-text-input id="effective_to" name="effective_to" type="date" class="mt-1 block w-full" value="{{ old(
                'effective_to',
                isset($curriculum->effective_to)
                    ? $curriculum->effective_to->format('Y-m-d')
                    : ''
            ) }}" />

        <x-input-error :messages="$errors->get('effective_to')" class="mt-2" />
    </div>

    <div class="flex items-center pt-7">
        <label class="inline-flex items-center">
            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('is_active', $curriculum->is_active ?? true))
            >

            <span class="ml-2 text-sm text-gray-600">
                Currículo activo
            </span>
        </label>
    </div>

</div>

<div class="mt-6">
    <x-input-label for="description" value="Descrição" />

    <textarea id="description" name="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Descrição do currículo...">{{ old('description', $curriculum->description ?? '') }}</textarea>

    <x-input-error :messages="$errors->get('description')" class="mt-2" />
</div>

<div class="mt-6 flex justify-end gap-3">
    <a href="{{ route('university.curricula.index') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">
        Cancelar
    </a>

    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
        {{ isset($curriculum) ? 'Actualizar currículo' : 'Criar currículo' }}
    </button>
</div>
