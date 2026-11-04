<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $courses = Course::query()
            ->with(['faculty', 'department'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('code', 'ILIKE', "%{$search}%")
                        ->orWhere('acronym', 'ILIKE', "%{$search}%");
                });
            })
            ->when($request->filled('faculty_id'), function ($query) use ($request) {
                $query->where('faculty_id', $request->faculty_id);
            })
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->where('department_id', $request->department_id);
            })
            ->when($request->filled('degree_type'), function ($query) use ($request) {
                $query->where('degree_type', $request->degree_type);
            })
            ->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $faculties = Faculty::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $departments = Department::query()
            ->where('is_active', true)
            ->with('faculty')
            ->orderBy('name')
            ->get();

        return view('university.courses.index', compact(
            'courses',
            'faculties',
            'departments'
        ));
    }

    public function create()
    {
        $faculties = Faculty::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $departments = Department::query()
            ->where('is_active', true)
            ->with('faculty')
            ->orderBy('name')
            ->get();

        return view('university.courses.create', compact(
            'faculties',
            'departments'
        ));
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $data = $this->validateData($request, $tenantId);

        Course::create([
            ...$data,
            'is_active' => true,
        ]);

        return redirect()
            ->route('university.courses.index')
            ->with('success', 'Curso cadastrado com sucesso.');
    }

    public function edit(int $id)
    {
        $course = Course::findOrFail($id);

        $faculties = Faculty::query()
            ->where('is_active', true)
            ->orWhere('id', $course->faculty_id)
            ->orderBy('name')
            ->get();

        $departments = Department::query()
            ->where('is_active', true)
            ->orWhere('id', $course->department_id)
            ->with('faculty')
            ->orderBy('name')
            ->get();

        return view('university.courses.edit', compact(
            'course',
            'faculties',
            'departments'
        ));
    }

    public function update(Request $request, int $id)
    {
        $course = Course::findOrFail($id);

        $data = $this->validateData(
            $request,
            auth()->user()->tenant_id,
            $course->id
        );

        $course->update($data);

        return redirect()
            ->route('university.courses.index')
            ->with('success', 'Curso actualizado com sucesso.');
    }

    public function updateStatus(int $id)
    {
        $course = Course::findOrFail($id);

        $course->update([
            'is_active' => !$course->is_active,
        ]);

        return back()->with('success', 'Estado do curso actualizado com sucesso.');
    }

    public function destroy(int $id)
    {
        $course = Course::findOrFail($id);

        if ($course->is_active) {
            return back()->withErrors([
                'course' => 'Desactive o curso antes de o eliminar.',
            ]);
        }

        $course->delete();

        return redirect()
            ->route('university.courses.index')
            ->with('success', 'Curso eliminado com sucesso.');
    }

    private function validateData(
        Request $request,
        int $tenantId,
        ?int $courseId = null
    ): array {
        $data = $request->validate([
            'faculty_id' => [
                'required',
                Rule::exists('faculties', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('is_active', true),
            ],

            'department_id' => [
                'nullable',
                Rule::exists('departments', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('is_active', true),
            ],

            'name' => [
                'required',
                'string',
                'max:200',
                Rule::unique('courses', 'name')
                    ->where('tenant_id', $tenantId)
                    ->ignore($courseId),
            ],

            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('courses', 'code')
                    ->where('tenant_id', $tenantId)
                    ->ignore($courseId),
            ],

            'acronym' => 'nullable|string|max:30',

            'degree_type' => [
                'required',
                Rule::in([
                    'licenciatura',
                    'bacharelato',
                    'mestrado',
                    'doutoramento',
                    'pos_graduacao',
                    'curso_tecnico',
                    'outro',
                ]),
            ],

            'duration_years' => 'nullable|integer|min:1|max:10',

            'duration_semesters' => 'nullable|integer|min:1|max:20',

            'study_regime' => [
                'required',
                Rule::in([
                    'presencial',
                    'pos_laboral',
                    'online',
                    'hibrido',
                ]),
            ],

            'description' => 'nullable|string',

            'is_accredited' => 'nullable|boolean',

            'accreditation_number' => 'nullable|string|max:100',

            'accreditation_date' => 'nullable|date',

            'accreditation_expiry' => 'nullable|date|after_or_equal:accreditation_date',
        ]);

        $data['is_accredited'] = $request->boolean('is_accredited');

        return $data;
    }
}
