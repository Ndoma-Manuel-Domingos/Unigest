<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use App\Models\AssessmentGrade;
use App\Models\CurriculumSubject;
use App\Models\Enrollment;
use App\Models\SubjectAssessment;
use App\Models\SubjectEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SubjectAssessmentController extends Controller
{
    public function index(string $academicClass)
    {

       $academicClass = AcademicClass::findOrFail($academicClass);

        $assessments = SubjectAssessment::with('curriculumSubject.subject')
            ->where('academic_class_id', $academicClass->id)
            ->orderBy('assessment_date')
            ->paginate(20);

        return view('university.assessments.index', compact(
            'academicClass',
            'assessments'
        ));
    }

    public function create(string $academicClass)
    {

        $academicClass = AcademicClass::findOrFail($academicClass);

        abort_unless($academicClass->is_active, 422, 'Esta turma está inactiva.');

        abort_unless(
            $academicClass->curriculum_id,
            422,
            'Esta turma não possui plano curricular.'
        );

        $subjects = CurriculumSubject::with('subject')
            ->where('curriculum_id', $academicClass->curriculum_id)
            ->where('is_active', true)
            ->orderBy('year_number')
            ->orderBy('semester_number')
            ->orderBy('order_number')
            ->get();

        return view('university.assessments.create', compact(
            'academicClass',
            'subjects'
        ));
    }

    public function store(Request $request, string $academicClass)
    {
        $validated = $request->validate([
            'curriculum_subject_id' => ['required', 'integer'],
            'title' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:test,work,exam,final_exam,participation,other'],
            'assessment_date' => ['required', 'date'],
            'weight' => ['required', 'numeric', 'gt:0', 'lte:100'],
            'max_grade' => ['required', 'numeric', 'gt:0', 'lte:100'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $academicClass = AcademicClass::findOrFail($academicClass);

        DB::transaction(function () use ($validated, $academicClass) {
            $lockedClass = AcademicClass::whereKey($academicClass->id)
                ->lockForUpdate()
                ->firstOrFail();

            abort_unless(
                $lockedClass->is_active && $lockedClass->curriculum_id,
                422,
                'A turma está inactiva ou não tem plano curricular.'
            );

            $subject = CurriculumSubject::whereKey($validated['curriculum_subject_id'])
                ->where('curriculum_id', $lockedClass->curriculum_id)
                ->where('is_active', true)
                ->firstOrFail();

            $existingWeight = SubjectAssessment::where(
                'academic_class_id',
                $lockedClass->id
            )
                ->where('curriculum_subject_id', $subject->id)
                ->sum('weight');

            if (($existingWeight + $validated['weight']) > 100) {
                throw ValidationException::withMessages([
                    'weight' => 'A soma das ponderações desta disciplina ultrapassaria 100%. Ponderação já utilizada: '
                        . number_format((float) $existingWeight, 2, ',', '.') . '%.',
                ]);
            }

            SubjectAssessment::create([
                'academic_class_id' => $lockedClass->id,
                'curriculum_subject_id' => $subject->id,
                'title' => $validated['title'],
                'type' => $validated['type'],
                'assessment_date' => $validated['assessment_date'],
                'weight' => $validated['weight'],
                'max_grade' => $validated['max_grade'],
                'description' => $validated['description'] ?? null,
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()
            ->route('university.assessments.index', $academicClass)
            ->with('success', 'Avaliação criada com sucesso.');
    }

    public function show(string $assessment)
    {
        $assessment = SubjectAssessment::findOrFail($assessment);

        $assessment->load([
            'academicClass',
            'curriculumSubject.subject',
            'grades.subjectEnrollment.enrollment.student',
        ]);

        $students = SubjectEnrollment::with('enrollment.student')
            ->whereIn('status', ['enrolled', 'approved', 'failed'])
            ->whereHas('enrollment', function ($query) use ($assessment) {
                $query->where('academic_class_id', $assessment->academic_class_id);
            })
            ->where('curriculum_subject_id', $assessment->curriculum_subject_id)
            ->get()
            ->sortBy(fn ($item) => $item->enrollment->student->name);

        $gradesByEnrollment = $assessment->grades->keyBy('subject_enrollment_id');

        return view('university.assessments.show', compact(
            'assessment',
            'students',
            'gradesByEnrollment'
        ));
    }

    public function saveGrades(Request $request, string $assessment)
    {
        $assessment = SubjectAssessment::findOrFail($assessment);

        if ($assessment->is_published) {
            return back()->with('error', 'Esta avaliação já foi publicada e não aceita alterações.');
        }

        $validated = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.subject_enrollment_id' => ['required', 'integer', 'distinct'],
            'grades.*.grade' => ['nullable', 'numeric', 'min:0'],
            'grades.*.observations' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($validated, $assessment) {
            $assessment = SubjectAssessment::whereKey($assessment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($assessment->is_published) {
                throw ValidationException::withMessages([
                    'grades' => 'A avaliação foi publicada. Não pode ser alterada.',
                ]);
            }

            $classEnrollmentIds = Enrollment::where(
                'academic_class_id',
                $assessment->academic_class_id
            )->pluck('id');

            foreach ($validated['grades'] as $row) {
                $subjectEnrollment = SubjectEnrollment::whereKey(
                    $row['subject_enrollment_id']
                )
                    ->whereIn('enrollment_id', $classEnrollmentIds)
                    ->where('curriculum_subject_id', $assessment->curriculum_subject_id)
                    ->whereIn('status', ['enrolled', 'approved', 'failed'])
                    ->first();

                if (!$subjectEnrollment) {
                    throw ValidationException::withMessages([
                        'grades' => 'Foi encontrada uma inscrição que não pertence a esta turma ou disciplina.',
                    ]);
                }

                if (
                    $row['grade'] !== null
                    && (float) $row['grade'] > (float) $assessment->max_grade
                ) {
                    throw ValidationException::withMessages([
                        'grades' => 'Uma nota ultrapassa a classificação máxima desta avaliação.',
                    ]);
                }

                AssessmentGrade::updateOrCreate(
                    [
                        'subject_assessment_id' => $assessment->id,
                        'subject_enrollment_id' => $subjectEnrollment->id,
                    ],
                    [
                        'grade' => $row['grade'],
                        'observations' => $row['observations'] ?? null,
                        'entered_by' => auth()->id(),
                    ]
                );
            }

            $this->recalculateFinalGrades($assessment);
        });

        return back()->with('success', 'Notas guardadas e classificações actualizadas.');
    }

    public function publish(string $assessment)
    {

        $assessment = SubjectAssessment::findOrFail($assessment);

        $assessment->load('grades');

        $studentsCount = SubjectEnrollment::where('status', 'enrolled')
            ->where('curriculum_subject_id', $assessment->curriculum_subject_id)
            ->whereHas('enrollment', function ($query) use ($assessment) {
                $query->where('academic_class_id', $assessment->academic_class_id);
            })
            ->count();

        $gradesCount = $assessment->grades()
            ->whereNotNull('grade')
            ->count();

        if ($studentsCount !== $gradesCount) {
            return back()->with(
                'error',
                'Não é possível publicar. Existem estudantes sem nota nesta avaliação.'
            );
        }

        $assessment->update(['is_published' => true]);

        return back()->with('success', 'Avaliação publicada com sucesso.');
    }


    public function gradeSheet(AcademicClass $academicClass, CurriculumSubject $curriculumSubject) 
    {

        $curriculumSubject = CurriculumSubject::findOrFail($curriculumSubject);
        $academicClass = AcademicClass::findOrFail($academicClass);

        abort_unless(
            (int) $academicClass->curriculum_id === (int) $curriculumSubject->curriculum_id,
            404
        );

        $students = SubjectEnrollment::with('enrollment.student')
            ->where('curriculum_subject_id', $curriculumSubject->id)
            ->whereIn('status', ['enrolled', 'approved', 'failed'])
            ->whereHas('enrollment', function ($query) use ($academicClass) {
                $query->where('academic_class_id', $academicClass->id);
            })
            ->get()
            ->sortBy(fn ($item) => $item->enrollment->student->name);

        $subject = $curriculumSubject->subject;

        return view('university.assessments.grade-sheet', compact(
            'academicClass',
            'curriculumSubject',
            'students',
            'subject'
        ));
    }

    private function recalculateFinalGrades(string $assessment): void
    {
        $assessment = SubjectAssessment::findOrFail($assessment);

        $assessments = SubjectAssessment::where(
            'academic_class_id',
            $assessment->academic_class_id
        )
            ->where('curriculum_subject_id', $assessment->curriculum_subject_id)
            ->get();

        $totalWeight = (float) $assessments->sum('weight');

        $enrollments = SubjectEnrollment::where(
            'curriculum_subject_id',
            $assessment->curriculum_subject_id
        )
            ->whereIn('status', ['enrolled', 'approved', 'failed'])
            ->whereHas('enrollment', function ($query) use ($assessment) {
                $query->where('academic_class_id', $assessment->academic_class_id);
            })
            ->with('grades.assessment')
            ->get();

        foreach ($enrollments as $subjectEnrollment) {
            $grades = $subjectEnrollment->grades
                ->whereIn('subject_assessment_id', $assessments->pluck('id'));

            $allEntered = $assessments->every(function ($item) use ($grades) {
                $grade = $grades->firstWhere('subject_assessment_id', $item->id);

                return $grade && $grade->grade !== null;
            });

            if (!$allEntered || round($totalWeight, 2) !== 100.00) {
                $subjectEnrollment->update(['final_grade' => null]);
                continue;
            }

            $finalGrade = 0;

            foreach ($assessments as $item) {
                $grade = $grades->firstWhere('subject_assessment_id', $item->id);

                $gradeOutOfTwenty = ((float) $grade->grade / (float) $item->max_grade) * 20;

                $finalGrade += $gradeOutOfTwenty * ((float) $item->weight / 100);
            }

            $finalGrade = round($finalGrade, 2);

            $subjectEnrollment->update([
                'final_grade' => $finalGrade,
                'status' => $finalGrade >= 10 ? 'approved' : 'failed',
            ]);
        }
    }
}
