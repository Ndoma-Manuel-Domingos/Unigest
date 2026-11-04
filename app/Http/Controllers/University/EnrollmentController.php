<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class EnrollmentController extends Controller
{
    private const STATUSES = [
        'pending',
        'active',
        'suspended',
        'cancelled',
        'completed',
    ];

    private const OCCUPYING_STATUSES = [
        'pending',
        'active',
        'suspended',
    ];

    public function index(Request $request)
    {
        $enrollments = Enrollment::with([
            'student',
            'academicYear',
            'course',
            'academicClass',
        ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('enrollment_number', 'ILIKE', "%{$search}%")
                        ->orWhereHas('student', function ($student) use ($search) {
                            $student->where('name', 'ILIKE', "%{$search}%")
                                ->orWhere('student_number', 'ILIKE', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('academic_year_id'), function ($query) use ($request) {
                $query->where('academic_year_id', $request->academic_year_id);
            })
            ->latest('enrollment_date')
            ->paginate(15)
            ->withQueryString();

        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        return view('university.enrollments.index', compact(
            'enrollments',
            'academicYears'
        ));
    }

    public function create()
    {
        $students = Student::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'student_number', 'name']);

        $academicYears = AcademicYear::where('is_active', true)
            ->orderByDesc('start_date')
            ->get();

        $courses = Course::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'duration_years']);

        $classes = AcademicClass::where('is_active', true)
            ->with(['academicYear:id,name', 'course:id,name'])
            ->orderBy('name')
            ->get();

        return view('university.enrollments.create', compact(
            'students',
            'academicYears',
            'courses',
            'classes'
        ));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'student_id' => ['required', 'integer'],
            'academic_year_id' => ['required', 'integer'],
            'course_id' => ['required', 'integer'],
            'academic_class_id' => ['required', 'integer'],
            'enrollment_date' => ['required', 'date'],
            'year_number' => ['required', 'integer', 'min:1', 'max:20'],
            'semester_number' => ['nullable', 'integer', 'min:1', 'max:3'],
            'enrollment_type' => [
                'required',
                Rule::in(['regular', 'transfer', 'reentry', 'special']),
            ],
            'observations' => ['nullable', 'string', 'max:5000'],
        ]);

        $tenantId = auth()->user()->tenant_id;

        $enrollment = DB::transaction(function () use ($validated, $tenantId) {
            // Bloqueia a turma para evitar excesso de matrículas simultâneas.
            $academicClass = AcademicClass::whereKey($validated['academic_class_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $student = Student::whereKey($validated['student_id'])->firstOrFail();
            $academicYear = AcademicYear::whereKey($validated['academic_year_id'])->firstOrFail();
            $course = Course::whereKey($validated['course_id'])->firstOrFail();

            if (!$student->is_active) {
                throw ValidationException::withMessages([
                    'student_id' => 'Este estudante está inactivo.',
                ]);
            }

            if (!$academicYear->is_active) {
                throw ValidationException::withMessages([
                    'academic_year_id' => 'O ano académico seleccionado está inactivo.',
                ]);
            }

            if (!$course->is_active) {
                throw ValidationException::withMessages([
                    'course_id' => 'O curso seleccionado está inactivo.',
                ]);
            }

            if (!$academicClass->is_active) {
                throw ValidationException::withMessages([
                    'academic_class_id' => 'A turma seleccionada está inactiva.',
                ]);
            }

            if (
                (int) $academicClass->academic_year_id !== (int) $academicYear->id
                || (int) $academicClass->course_id !== (int) $course->id
            ) {
                throw ValidationException::withMessages([
                    'academic_class_id' => 'A turma não pertence ao ano académico e curso seleccionados.',
                ]);
            }

            if (
                $course->duration_years
                && $validated['year_number'] > $course->duration_years
            ) {
                throw ValidationException::withMessages([
                    'year_number' => 'O ano curricular ultrapassa a duração do curso.',
                ]);
            }

            if (
                $academicClass->year_number
                && (int) $academicClass->year_number !== (int) $validated['year_number']
            ) {
                throw ValidationException::withMessages([
                    'year_number' => 'O ano curricular não corresponde ao ano da turma.',
                ]);
            }

            if (
                $academicClass->semester_number
                && $validated['semester_number']
                && (int) $academicClass->semester_number !== (int) $validated['semester_number']
            ) {
                throw ValidationException::withMessages([
                    'semester_number' => 'O semestre não corresponde ao semestre da turma.',
                ]);
            }

            $alreadyEnrolled = Enrollment::where('student_id', $student->id)
                ->where('academic_year_id', $academicYear->id)
                ->exists();

            if ($alreadyEnrolled) {
                throw ValidationException::withMessages([
                    'student_id' => 'Este estudante já possui uma matrícula neste ano académico.',
                ]);
            }

            $occupied = Enrollment::where('academic_class_id', $academicClass->id)
                ->whereIn('status', self::OCCUPYING_STATUSES)
                ->count();

            if ($academicClass->capacity && $occupied >= $academicClass->capacity) {
                throw ValidationException::withMessages([
                    'academic_class_id' => 'A turma já atingiu a sua capacidade máxima.',
                ]);
            }

            // O bloqueio da universidade sincroniza a geração dos números.
            University::whereKey($tenantId)->lockForUpdate()->firstOrFail();

            $sequence = Enrollment::whereYear('enrollment_date', now()->year)->count() + 1;

            $number = 'MAT-' . now()->year . '-' . str_pad(
                $sequence,
                6,
                '0',
                STR_PAD_LEFT
            );

            return Enrollment::create([
                'student_id' => $student->id,
                'academic_year_id' => $academicYear->id,
                'course_id' => $course->id,
                'academic_class_id' => $academicClass->id,
                'curriculum_id' => $academicClass->curriculum_id,
                'enrollment_number' => $number,
                'enrollment_date' => $validated['enrollment_date'],
                'year_number' => $validated['year_number'],
                'semester_number' => $validated['semester_number'] ?? null,
                'enrollment_type' => $validated['enrollment_type'],
                'status' => 'pending',
                'observations' => $validated['observations'] ?? null,
                'registered_by' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('university.enrollments.show', $enrollment)
            ->with('success', 'Matrícula registada com sucesso.');
    }

    public function show(string $id)
    {
        $enrollment = Enrollment::findOrFail($id); 

        $enrollment->load([
            'student',
            'academicYear',
            'course',
            'academicClass',
            'curriculum',
            'registeredBy',
        ]);

        return view('university.enrollments.show', compact('enrollment'));
    }

    public function updateStatus(Request $request, string $id)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(self::STATUSES)],
        ]);

        $enrollment = Enrollment::findOrFail($id);

        DB::transaction(function () use ($enrollment, $validated) {
            $academicClass = AcademicClass::whereKey($enrollment->academic_class_id)
                ->lockForUpdate()
                ->firstOrFail();

            $newStatus = $validated['status'];

            if (
                in_array($newStatus, self::OCCUPYING_STATUSES, true)
                && !in_array($enrollment->status, self::OCCUPYING_STATUSES, true)
            ) {
                $occupied = Enrollment::where('academic_class_id', $academicClass->id)
                    ->whereIn('status', self::OCCUPYING_STATUSES)
                    ->count();

                if ($academicClass->capacity && $occupied >= $academicClass->capacity) {
                    throw ValidationException::withMessages([
                        'status' => 'Não é possível activar esta matrícula: a turma está lotada.',
                    ]);
                }
            }

            $enrollment->update(['status' => $newStatus]);
        });

        return back()->with('success', 'Estado da matrícula actualizado.');
    }

    public function print(string $id)
    {
        $enrollment = Enrollment::findOrFail($id); 
        
        $enrollment->load([
            'student',
            'academicYear',
            'course',
            'academicClass',
            'curriculum',
            'registeredBy',
        ]);

        return view('university.enrollments.print', compact('enrollment'));
    }
}
