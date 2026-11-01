<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\ClassSession;
use App\Models\SubjectEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function show(ClassSession $session)
    {
        $session->load([
            'academicClass.course',
            'academicClass.academicYear',
            'curriculumSubject.subject',
        ]);

        if ($session->status === 'cancelled') {
            return back()->with(
                'error',
                'Esta aula foi cancelada.'
            );
        }

        $subjectEnrollments = SubjectEnrollment::query()
            ->with([
                'enrollment.student',
            ])
            ->where('curriculum_subject_id', $session->curriculum_subject_id)
            ->whereHas('enrollment', function ($query) use ($session) {
                $query->where(
                    'academic_class_id',
                    $session->academic_class_id
                );
            })
            ->whereIn('status', [
                'enrolled',
                'approved',
                'failed',
            ])
            ->get()
            ->sortBy(function ($item) {
                return mb_strtolower(
                    $item->enrollment->student->name
                );
            })
            ->values();

        $records = AttendanceRecord::query()
            ->where('class_session_id', $session->id)
            ->get()
            ->keyBy('subject_enrollment_id');

        return view(
            'university.attendance.show',
            compact(
                'session',
                'subjectEnrollments',
                'records'
            )
        );
    }

    public function save(
        Request $request,
        ClassSession $session
    ) {
        if ($session->status === 'cancelled') {
            return back()->with(
                'error',
                'Não é possível registar frequência numa aula cancelada.'
            );
        }

        $validated = $request->validate([
            'attendance' => [
                'required',
                'array',
            ],

            'attendance.*.status' => [
                'required',
                'in:present,absent,late,excused',
            ],

            'attendance.*.arrival_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'attendance.*.justification' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $subjectEnrollments = SubjectEnrollment::query()
            ->whereIn(
                'id',
                array_keys($validated['attendance'])
            )
            ->where('curriculum_subject_id', $session->curriculum_subject_id)
            ->whereHas('enrollment', function ($query) use ($session) {
                $query->where(
                    'academic_class_id',
                    $session->academic_class_id
                );
            })
            ->whereIn('status', [
                'enrolled',
                'approved',
                'failed',
            ])
            ->pluck('id')
            ->all();

        DB::transaction(function () use (
            $validated,
            $subjectEnrollments,
            $session
        ) {
            foreach ($validated['attendance'] as $subjectEnrollmentId => $data) {

                if (!in_array(
                    (int) $subjectEnrollmentId,
                    $subjectEnrollments,
                    true
                )) {
                    continue;
                }

                if (
                    $data['status'] === 'excused' &&
                    empty($data['justification'])
                ) {
                    abort(
                        422,
                        'A justificação é obrigatória para faltas justificadas.'
                    );
                }

                $arrivalTime = null;

                if ($data['status'] === 'late') {
                    $arrivalTime = $data['arrival_time'] ?? null;
                }

                AttendanceRecord::updateOrCreate(
                    [
                        'class_session_id' => $session->id,
                        'subject_enrollment_id' => $subjectEnrollmentId,
                    ],
                    [
                        'status' => $data['status'],
                        'arrival_time' => $arrivalTime,
                        'justification' =>
                            $data['status'] === 'excused'
                                ? ($data['justification'] ?? null)
                                : null,
                        'recorded_by' => Auth::id(),
                    ]
                );
            }

            $session->update([
                'status' => 'completed',
                'recorded_by' => Auth::id(),
            ]);
        });

        return back()->with(
            'success',
            'Frequência registada com sucesso.'
        );
    }

    public function report(
        Request $request,
        SubjectEnrollment $subjectEnrollment
    ) {
        $subjectEnrollment->load([
            'enrollment.student',
            'curriculumSubject.subject',
        ]);

        $records = AttendanceRecord::query()
            ->with('classSession')
            ->where('subject_enrollment_id', $subjectEnrollment->id)
            ->whereHas('classSession', function ($query) {
                $query->where('status', 'completed');
            })
            ->orderByDesc(
                ClassSession::query()
                    ->select('session_date')
                    ->whereColumn(
                        'class_sessions.id',
                        'attendance_records.class_session_id'
                    )
                    ->limit(1)
            )
            ->get();

        $total = $records->count();

        $present = $records
            ->where('status', 'present')
            ->count();

        $late = $records
            ->where('status', 'late')
            ->count();

        $absent = $records
            ->where('status', 'absent')
            ->count();

        $excused = $records
            ->where('status', 'excused')
            ->count();

        $effectiveTotal = $total - $excused;

        $percentage = $effectiveTotal > 0
            ? round(
                (($present + $late) / $effectiveTotal) * 100,
                2
            )
            : 0;

        return view(
            'university.attendance.report',
            compact(
                'subjectEnrollment',
                'records',
                'total',
                'present',
                'late',
                'absent',
                'excused',
                'percentage'
            )
        );
    }
}
