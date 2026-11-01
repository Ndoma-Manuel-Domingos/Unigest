<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use App\Models\ClassSchedule;
use App\Models\CurriculumSubject;
use App\Models\Room;
use App\Models\Teacher;
use Illuminate\Http\Request;

class ClassScheduleController extends Controller
{
    public function index(string $academicClass)
    {
        $academicClass = AcademicClass::findOrFail($academicClass);

        $schedules = ClassSchedule::query()
            ->with([
                'curriculumSubject.subject',
                'teacher',
                'room',
            ])
            ->where(
                'academic_class_id',
                $academicClass->id
            )
            ->orderBy('day_of_week')
            ->orderBy('start_time')
            ->get();

        return view(
            'university.schedules.index',
            compact(
                'academicClass',
                'schedules'
            )
        );
    }

    public function create(string $academicClass)
    {
        $academicClass = AcademicClass::findOrFail($academicClass);

        abort_unless($academicClass->is_active, 422);

        $subjects = CurriculumSubject::query()
            ->with('subject')
            ->where(
                'curriculum_id',
                $academicClass->curriculum_id
            )
            ->where('is_active', true)
            ->orderBy('year_number')
            ->orderBy('semester_number')
            ->orderBy('order_number')
            ->get();

        $teachers = Teacher::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $rooms = Room::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'university.schedules.create',
            compact(
                'academicClass',
                'subjects',
                'teachers',
                'rooms'
            )
        );
    }

    public function store(
        Request $request,
        string $academicClass
    ) {
        $validated = $request->validate([
            'curriculum_subject_id' => [
                'required',
                'integer',
            ],

            'teacher_id' => [
                'required',
                'integer',
            ],

            'room_id' => [
                'nullable',
                'integer',
            ],

            'day_of_week' => [
                'required',
                'integer',
                'between:1,7',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'shift' => [
                'nullable',
                'in:manha,tarde,noite,integral,pos_laboral',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'ends_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],

            'observations' => [
                'nullable',
                'string',
            ],
        ]);

        $academicClass = AcademicClass::findOrFail($academicClass);

        $subject = CurriculumSubject::query()
            ->whereKey($validated['curriculum_subject_id'])
            ->where(
                'curriculum_id',
                $academicClass->curriculum_id
            )
            ->where('is_active', true)
            ->first();

        if (!$subject) {
            return back()
                ->withInput()
                ->with('error', 'Disciplina inválida para esta turma.');
        }

        $teacher = Teacher::query()
            ->whereKey($validated['teacher_id'])
            ->where('is_active', true)
            ->first();

        if (!$teacher) {
            return back()
                ->withInput()
                ->with('error', 'Professor inválido.');
        }

        if (!empty($validated['room_id'])) {
            $room = Room::query()
                ->whereKey($validated['room_id'])
                ->where('is_active', true)
                ->first();

            if (!$room) {
                return back()
                    ->withInput()
                    ->with('error', 'Sala inválida.');
            }
        }

        if ($this->hasConflict(
            $validated,
            $academicClass->id
        )) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Existe um conflito de horário para a turma, professor ou sala.'
                );
        }

        ClassSchedule::create([
            ...$validated,
            'academic_class_id' => $academicClass->id,
        ]);

        return redirect()
            ->route(
                'university.schedules.index',
                $academicClass
            )
            ->with(
                'success',
                'Horário criado com sucesso.'
            );
    }

    public function edit(ClassSchedule $schedule)
    {
        $schedule = ClassSchedule::findOrFail($schedule);

        $academicClass = $schedule->academicClass;

        $subjects = CurriculumSubject::query()
            ->with('subject')
            ->where(
                'curriculum_id',
                $academicClass->curriculum_id
            )
            ->where('is_active', true)
            ->orderBy('year_number')
            ->orderBy('semester_number')
            ->orderBy('order_number')
            ->get();

        $teachers = Teacher::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $rooms = Room::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'university.schedules.edit',
            compact(
                'schedule',
                'academicClass',
                'subjects',
                'teachers',
                'rooms'
            )
        );
    }

    public function update(
        Request $request,
        ClassSchedule $schedule
    ) {
        $validated = $request->validate([
            'curriculum_subject_id' => [
                'required',
                'integer',
            ],

            'teacher_id' => [
                'required',
                'integer',
            ],

            'room_id' => [
                'nullable',
                'integer',
            ],

            'day_of_week' => [
                'required',
                'integer',
                'between:1,7',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'shift' => [
                'nullable',
                'in:manha,tarde,noite,integral,pos_laboral',
            ],

            'status' => [
                'required',
                'in:active,inactive',
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'ends_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],

            'observations' => [
                'nullable',
                'string',
            ],
        ]);

        $schedule = ClassSchedule::findOrFail($schedule);

        $academicClass = $schedule->academicClass;

        if ($this->hasConflict(
            $validated,
            $academicClass->id,
            $schedule->id
        )) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Existe um conflito de horário para a turma, professor ou sala.'
                );
        }

        $schedule->update($validated);

        return redirect()
            ->route(
                'university.schedules.index',
                $academicClass
            )
            ->with(
                'success',
                'Horário actualizado com sucesso.'
            );
    }

    public function destroy(ClassSchedule $schedule)
    {
        $schedule = ClassSchedule::findOrFail($schedule);

        $academicClass = $schedule->academicClass;

        $schedule->delete();

        return redirect()
            ->route(
                'university.schedules.index',
                $academicClass
            )
            ->with(
                'success',
                'Horário eliminado com sucesso.'
            );
    }

    private function hasConflict(
        array $data,
        int $academicClassId,
        ?int $ignoreId = null
    ): bool {
        return ClassSchedule::query()
            ->where('day_of_week', $data['day_of_week'])
            ->where('status', 'active')
            ->when(
                $ignoreId,
                fn ($query) =>
                    $query->where('id', '!=', $ignoreId)
            )
            ->where(function ($query) use ($data) {
                $query->where(function ($q) use ($data) {
                    $q->where(
                        'start_time',
                        '<',
                        $data['end_time']
                    )->where(
                        'end_time',
                        '>',
                        $data['start_time']
                    );
                });
            })
            ->where(function ($query) use (
                $data,
                $academicClassId
            ) {
                $query
                    ->where(
                        'academic_class_id',
                        $academicClassId
                    )
                    ->orWhere(
                        'teacher_id',
                        $data['teacher_id']
                    );

                if (!empty($data['room_id'])) {
                    $query->orWhere(
                        'room_id',
                        $data['room_id']
                    );
                }
            })
            ->exists();
    }
}
