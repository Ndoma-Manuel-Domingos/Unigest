<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\ScholarshipType;
use App\Models\Student;
use App\Models\StudentScholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StudentScholarshipController extends Controller
{
    public function index(Request $request)
    {
        $scholarships = StudentScholarship::with([
            'student',
            'scholarshipType',
            'academicYear',
            'approver',
        ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->whereHas('student', function ($studentQuery) use ($search) {
                        $studentQuery->where('name', 'ILIKE', "%{$search}%");
                    })->orWhereHas('scholarshipType', function ($typeQuery) use ($search) {
                        $typeQuery->where('name', 'ILIKE', "%{$search}%");
                    });
                });
            })
            ->when($request->filled('status'), fn ($q) =>
                $q->where('status', $request->status)
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('university.scholarships.students.index', compact('scholarships'));
    }

    public function create()
    {
        $students = Student::orderBy('name')->get();
        $scholarshipTypes = ScholarshipType::where('is_active', true)
            ->orderBy('name')
            ->get();
        $academicYears = AcademicYear::orderByDesc('id')->get();

        return view('university.scholarships.students.create', compact(
            'students',
            'scholarshipTypes',
            'academicYears'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer'],
            'scholarship_type_id' => ['required', 'integer'],
            'academic_year_id' => ['nullable', 'integer'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'application_reason' => ['nullable', 'string', 'max:5000'],
        ]);

        // Confirma que estudante, bolsa e ano académico pertencem à universidade actual.
        $student = Student::findOrFail($data['student_id']);

        $scholarshipType = ScholarshipType::where('is_active', true)
            ->findOrFail($data['scholarship_type_id']);

        if (!empty($data['academic_year_id'])) {
            AcademicYear::findOrFail($data['academic_year_id']);
        }

        StudentScholarship::create([
            ...$data,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('university.student-scholarships.index')
            ->with('success', 'Pedido de bolsa registado com sucesso.');
    }

    public function show(string $studentScholarship)
    {
        $studentScholarship = StudentScholarship::findOrFail($studentScholarship); 

        $studentScholarship->load([
            'student',
            'scholarshipType',
            'academicYear',
            'approver',
            'invoices',
        ]);

        return view(
            'university.scholarships.students.show',
            compact('studentScholarship')
        );
    }

    public function approve(string $studentScholarship)
    {
        $studentScholarship = StudentScholarship::findOrFail($studentScholarship);

        DB::transaction(function () use ($studentScholarship) {
            $scholarship = StudentScholarship::whereKey($studentScholarship->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($scholarship->status !== 'pending') {
                abort(422, 'Apenas pedidos pendentes podem ser aprovados.');
            }

            $type = $scholarship->scholarshipType;

            if (!$type || !$type->is_active) {
                abort(422, 'O tipo de bolsa está inativo.');
            }

            $scholarship->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approved_value' => $type->value,
            ]);
        });

        return back()->with('success', 'Bolsa aprovada com sucesso.');
    }

    public function reject(Request $request, string $studentScholarship)
    {
        $studentScholarship = StudentScholarship::findOrFail($studentScholarship);

        $data = $request->validate([
            'decision_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($studentScholarship->status !== 'pending') {
            return back()->with('error', 'Apenas pedidos pendentes podem ser rejeitados.');
        }

        $studentScholarship->update([
            'status' => 'rejected',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'decision_notes' => $data['decision_notes'] ?? null,
        ]);

        return back()->with('success', 'Pedido de bolsa rejeitado.');
    }

    public function suspend(Request $request, string $studentScholarship)
    {
        $studentScholarship = StudentScholarship::findOrFail($studentScholarship);

        $data = $request->validate([
            'decision_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($studentScholarship->status !== 'approved') {
            return back()->with('error', 'Apenas bolsas aprovadas podem ser suspensas.');
        }

        $studentScholarship->update([
            'status' => 'suspended',
            'decision_notes' => $data['decision_notes'] ?? null,
        ]);

        return back()->with('success', 'Bolsa suspensa com sucesso.');
    }
}
