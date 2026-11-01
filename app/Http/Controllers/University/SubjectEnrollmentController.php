<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\CurriculumSubject;
use App\Models\Enrollment;
use App\Models\SubjectEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubjectEnrollmentController extends Controller
{
    private const STATUSES = [
        'enrolled',
        'approved',
        'failed',
        'withdrawn',
    ];

    public function index(string $enrollment)
    {

        $enrollment = Enrollment::findOrFail($enrollment);

        $enrollment->load([
            'student',
            'course',
            'academicYear',
            'academicClass',
            'curriculum',
        ]);

        $subjects = SubjectEnrollment::with('curriculumSubject.subject')
            ->where('enrollment_id', $enrollment->id)
            ->orderBy('created_at')
            ->get();

        return view(
            'university.subject-enrollments.index',
            compact('enrollment', 'subjects')
        );
    }

    public function create(string $enrollment)
    {
        $enrollment = Enrollment::findOrFail($enrollment);
        
        $this->ensureEnrollmentCanRegisterSubjects($enrollment->id);

        if (!$enrollment->curriculum_id) {
            return back()->with(
                'error',
                'Esta matrícula não possui um plano curricular associado.'
            );
        }

        $curriculumSubjects = CurriculumSubject::with([
            'subject',
            'prerequisites.subject',
        ])
            ->where('curriculum_id', $enrollment->curriculum_id)
            ->where('is_active', true)
            ->where('year_number', $enrollment->year_number)
            ->when($enrollment->semester_number, function ($query) use ($enrollment) {
                $query->where('semester_number', $enrollment->semester_number);
            })
            ->orderBy('order_number')
            ->get();

        $alreadyRegistered = SubjectEnrollment::where(
            'enrollment_id',
            $enrollment->id
        )->pluck('curriculum_subject_id')->toArray();

        $completedSubjects = SubjectEnrollment::where('status', 'approved')
            ->whereHas('enrollment', function ($query) use ($enrollment) {
                $query->where('student_id', $enrollment->student_id);
            })
            ->pluck('curriculum_subject_id')
            ->unique()
            ->toArray();


        return view('university.subject-enrollments.create', compact(
            'enrollment',
            'curriculumSubjects',
            'alreadyRegistered',
            'completedSubjects'
        ));
    }

    public function store(Request $request, string $enrollment)
    {

        $enrollment = Enrollment::findOrFail($enrollment);

        $this->ensureEnrollmentCanRegisterSubjects($enrollment->id);

        $validated = $request->validate([
            'curriculum_subject_ids' => ['required', 'array', 'min:1'],
            'curriculum_subject_ids.*' => ['required', 'integer', 'distinct'],
            'enrollment_date' => ['required', 'date'],
        ]);

        DB::transaction(function () use ($validated, $enrollment) {
            $ids = $validated['curriculum_subject_ids'];

            $subjects = CurriculumSubject::with('prerequisites')
                ->where('curriculum_id', $enrollment->curriculum_id)
                ->where('year_number', $enrollment->year_number)
                ->when($enrollment->semester_number, function ($query) use ($enrollment) {
                    $query->where('semester_number', $enrollment->semester_number);
                })
                ->where('is_active', true)
                ->whereIn('id', $ids)
                ->get();

            if ($subjects->count() !== count($ids)) {
                throw ValidationException::withMessages([
                    'curriculum_subject_ids' =>
                        'Uma ou mais disciplinas não pertencem ao plano curricular, ano ou semestre desta matrícula.',
                ]);
            }

            $existing = SubjectEnrollment::where('enrollment_id', $enrollment->id)
                ->whereIn('curriculum_subject_id', $ids)
                ->exists();

            if ($existing) {
                throw ValidationException::withMessages([
                    'curriculum_subject_ids' =>
                        'Uma ou mais disciplinas já estão inscritas nesta matrícula.',
                ]);
            }

            $completedIds = SubjectEnrollment::where('status', 'approved')
                ->whereHas('enrollment', function ($query) use ($enrollment) {
                    $query->where('student_id', $enrollment->student_id);
                })
                ->pluck('curriculum_subject_id')
                ->unique()
                ->toArray();

            $selectedIds = collect($ids)->map(fn ($id) => (int) $id)->all();

            foreach ($subjects as $subject) {
                foreach ($subject->prerequisites as $prerequisite) {
                    if (!in_array($prerequisite->id, $completedIds, true)
                        && !in_array($prerequisite->id, $selectedIds, true)) {
                        throw ValidationException::withMessages([
                            'curriculum_subject_ids' =>
                                'A disciplina "' . $subject->subject->name
                                . '" exige a conclusão de "' . $prerequisite->subject->name . '".',
                        ]);
                    }
                }
            }

            foreach ($subjects as $subject) {
                SubjectEnrollment::create([
                    'enrollment_id' => $enrollment->id,
                    'curriculum_subject_id' => $subject->id,
                    'enrollment_date' => $validated['enrollment_date'],
                    'status' => 'enrolled',
                    'registered_by' => auth()->id(),
                ]);
            }
        });

        return redirect()
            ->route('university.subject-enrollments.index', $enrollment)
            ->with('success', 'Disciplinas inscritas com sucesso.');
    }

    public function updateStatus(
        Request $request,
        string $enrollment,
        string $subjectEnrollment
    ) {
        $validated = $request->validate([
            'status' => ['required', 'in:enrolled,approved,failed,withdrawn'],
            'final_grade' => ['nullable', 'numeric', 'min:0', 'max:20'],
            'observations' => ['nullable', 'string', 'max:5000'],
        ]);

        $enrollment = Enrollment::findOrFail($enrollment);
        $subjectEnrollment = SubjectEnrollment::findOrFail($subjectEnrollment);


        abort_unless(
            (int) $subjectEnrollment->enrollment_id === (int) $enrollment->id,
            404
        );

        if ($validated['status'] === 'approved' && $validated['final_grade'] === null) {
            throw ValidationException::withMessages([
                'final_grade' => 'Indica a nota final para aprovar a disciplina.',
            ]);
        }

        $subjectEnrollment->update([
            'status' => $validated['status'],
            'final_grade' => $validated['final_grade'] ?? null,
            'observations' => $validated['observations'] ?? null,
        ]);

        return back()->with('success', 'Inscrição na disciplina actualizada.');
    }

    public function destroy(string $enrollment, string $subjectEnrollment) 
    {
        $enrollment = Enrollment::findOrFail($enrollment);
        $subjectEnrollment = SubjectEnrollment::findOrFail($subjectEnrollment);

        abort_unless(
            (int) $subjectEnrollment->enrollment_id === (int) $enrollment->id,
            404
        );

        if ($subjectEnrollment->status === 'approved') {
            return back()->with(
                'error',
                'Não é possível remover uma disciplina já aprovada.'
            );
        }

        $subjectEnrollment->delete();

        return back()->with('success', 'Inscrição removida com sucesso.');
    }

    private function ensureEnrollmentCanRegisterSubjects(string $enrollment): void
    {
        $enrollment = Enrollment::findOrFail($enrollment);

        abort_unless(in_array($enrollment->status, ['pending', 'active'], true),403,'Esta matrícula não permite inscrições em disciplinas.');

        abort_unless($enrollment->curriculum_id !== null,422,'Esta matrícula não possui plano curricular.');
    }
}
 
