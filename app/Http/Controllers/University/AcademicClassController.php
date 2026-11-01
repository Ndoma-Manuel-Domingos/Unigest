<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Curriculum;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicClassController extends Controller
{
    public function index(Request $request)
    {
        $classes = AcademicClass::query()
            ->with(['academicYear', 'course', 'curriculum'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%");
                });
            })
            ->when($request->filled('academic_year_id'), fn ($q) =>
                $q->where('academic_year_id', $request->academic_year_id))
            ->when($request->filled('course_id'), fn ($q) =>
                $q->where('course_id', $request->course_id))
            ->when($request->filled('shift'), fn ($q) =>
                $q->where('shift', $request->shift))
            ->when($request->filled('status'), fn ($q) =>
                $q->where('is_active', $request->status === 'active'))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        $courses = Course::where('is_active', true)->orderBy('name')->get();

        return view('university.classes.index', compact(
            'classes',
            'academicYears',
            'courses'
        ));
    }

    public function create()
    {
        return view('university.classes.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        AcademicClass::create($data);

        return redirect()
            ->route('university.classes.index')
            ->with('success', 'Turma registada com sucesso.');
    }

    public function edit(string $academicClass)
    {
        $academicClass = AcademicClass::findOrFail($academicClass);

        return view('university.classes.edit', array_merge(
            $this->formData(),
            ['academicClass' => $academicClass]
        ));
    }

    public function update(Request $request, string $academicClass)
    {
        $academicClass = AcademicClass::findOrFail($academicClass);

        $data = $this->validateData($request, $academicClass);

        $academicClass->update($data);

        return redirect()
            ->route('university.classes.index')
            ->with('success', 'Turma actualizada com sucesso.');
    }

    public function updateStatus(string $academicClass)
    {
        $academicClass = AcademicClass::findOrFail($academicClass);

        $academicClass->update([
            'is_active' => !$academicClass->is_active,
        ]);

        return back()->with('success', 'Estado da turma actualizado.');
    }

    public function destroy(string $academicClass)
    {
        // Quando o módulo de matrículas estiver implementado,
        // impedir a eliminação de turmas com estudantes inscritos.

        $academicClass = AcademicClass::findOrFail($academicClass);

        if ($academicClass->enrollments()->exists()) {
            return back()->with(
                'error',
                'Não é possível eliminar esta turma porque existem matrículas associadas.'
            );
        }

        $academicClass->delete();

        return redirect()
            ->route('university.classes.index')
            ->with('success', 'Turma eliminada com sucesso.');
    }

    private function formData(): array
    {
        return [
            'academicYears' => AcademicYear::where('is_active', true)
                ->orderByDesc('start_date')
                ->get(),

            'courses' => Course::where('is_active', true)
                ->orderBy('name')
                ->get(),

            'curricula' => Curriculum::where('is_active', true)
                ->with('course')
                ->orderBy('name')
                ->get(),
        ];
    }

    private function validateData(
        Request $request,
        ?AcademicClass $academicClass = null
    ): array {
        $tenantId = auth()->user()->tenant_id;

        $data = $request->validate([
            'academic_year_id' => [
                'required',
                Rule::exists('academic_years', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('is_active', true),
            ],

            'course_id' => [
                'required',
                Rule::exists('courses', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('is_active', true),
            ],

            'curriculum_id' => [
                'nullable',
                Rule::exists('curricula', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('is_active', true),
            ],

            'name' => ['required', 'string', 'max:150'],

            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('academic_classes', 'code')
                    ->where('tenant_id', $tenantId)
                    ->ignore($academicClass?->id),
            ],

            'year_number' => ['required', 'integer', 'min:1', 'max:10'],
            'semester_number' => ['required', 'integer', 'min:1', 'max:3'],

            'shift' => [
                'required',
                Rule::in(['manha', 'tarde', 'noite', 'integral', 'pos_laboral', 'outro']),
            ],

            'study_regime' => [
                'required',
                Rule::in(['presencial', 'online', 'hibrido']),
            ],

            'capacity' => ['required', 'integer', 'min:1', 'max:10000'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $course = Course::findOrFail($data['course_id']);

        if (!empty($data['curriculum_id'])) {
            $curriculum = Curriculum::findOrFail($data['curriculum_id']);

            if ((int) $curriculum->course_id !== (int) $course->id) {
                abort(422, 'O plano curricular seleccionado não pertence ao curso escolhido.');
            }
        }

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
