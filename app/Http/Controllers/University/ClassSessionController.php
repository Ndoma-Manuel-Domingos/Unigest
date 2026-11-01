<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\AcademicClass;
use App\Models\ClassSession;
use App\Models\CurriculumSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClassSessionController extends Controller
{
    public function index(string $academicClass)
    {
        $academicClass = AcademicClass::findOrFail($academicClass); 

        $academicClass->load([
            'course',
            'academicYear',
        ]);

        $sessions = ClassSession::query()
            ->with('curriculumSubject.subject')
            ->where('academic_class_id', $academicClass->id)
            ->orderByDesc('session_date')
            ->orderByDesc('start_time')
            ->paginate(20);

        return view('university.attendance.sessions.index', compact(
            'academicClass',
            'sessions'
        ));
    }

    public function create(string $academicClass)
    {
        $academicClass = AcademicClass::findOrFail($academicClass); 

        abort_unless($academicClass->is_active, 422);

        if (!$academicClass->curriculum_id) {
            return back()->with('error', 'Esta turma não possui plano curricular.');
        }

        $subjects = CurriculumSubject::query()
            ->with('subject')
            ->where('curriculum_id', $academicClass->curriculum_id)
            ->where('is_active', true)
            ->orderBy('year_number')
            ->orderBy('semester_number')
            ->orderBy('order_number')
            ->get();

        return view('university.attendance.sessions.create', compact(
            'academicClass',
            'subjects'
        ));
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

            'session_date' => [
                'required',
                'date',
            ],

            'start_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'end_time' => [
                'nullable',
                'date_format:H:i',
                'after:start_time',
            ],

            'topic' => [
                'nullable',
                'string',
                'max:255',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:scheduled,completed,cancelled',
            ],
        ]);

        $academicClass = AcademicClass::findOrFail($academicClass); 

        if (!$academicClass->is_active) {
            return back()
                ->withInput()
                ->with('error', 'A turma está inativa.');
        }

        if (!$academicClass->curriculum_id) {
            return back()
                ->withInput()
                ->with('error', 'A turma não possui plano curricular.');
        }

        $subject = CurriculumSubject::query()
            ->whereKey($validated['curriculum_subject_id'])
            ->where('curriculum_id', $academicClass->curriculum_id)
            ->where('is_active', true)
            ->first();

        if (!$subject) {
            return back()
                ->withInput()
                ->with('error', 'A disciplina selecionada não pertence ao plano curricular da turma.');
        }

        $session = ClassSession::create([
            'academic_class_id' => $academicClass->id,
            'curriculum_subject_id' => $subject->id,
            'session_date' => $validated['session_date'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'topic' => $validated['topic'] ?? null,
            'content' => $validated['content'] ?? null,
            'status' => $validated['status'],
            'recorded_by' => Auth::id(),
        ]);

        return redirect()
            ->route('university.attendance.sessions.show', $session)
            ->with('success', 'Aula criada com sucesso.');
    }

    public function show(string $session)
    {
        $session = ClassSession::findOrFail($session); 

        $session->load([
            'academicClass.course',
            'curriculumSubject.subject',
        ]);

        return redirect()->route(
            'university.attendance.show',
            $session
        );
    }

    public function edit(string $session)
    {
        $session = ClassSession::findOrFail($session); 
        
        $academicClass = $session->academicClass;

        if (!$academicClass->curriculum_id) {
            return back()->with('error', 'A turma não possui plano curricular.');
        }

        $subjects = CurriculumSubject::query()
            ->with('subject')
            ->where('curriculum_id', $academicClass->curriculum_id)
            ->where('is_active', true)
            ->orderBy('year_number')
            ->orderBy('semester_number')
            ->orderBy('order_number')
            ->get();

        return view('university.attendance.sessions.edit', compact(
            'session',
            'academicClass',
            'subjects'
        ));
    }

    public function update(
        Request $request,
        string $session
    ) {
        $validated = $request->validate([
            'curriculum_subject_id' => [
                'required',
                'integer',
            ],

            'session_date' => [
                'required',
                'date',
            ],

            'start_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'end_time' => [
                'nullable',
                'date_format:H:i',
                'after:start_time',
            ],

            'topic' => [
                'nullable',
                'string',
                'max:255',
            ],

            'content' => [
                'nullable',
                'string',
            ],

            'status' => [
                'required',
                'in:scheduled,completed,cancelled',
            ],
        ]);

        $session = ClassSession::findOrFail($session); 

        $academicClass = $session->academicClass;

        $subject = CurriculumSubject::query()
            ->whereKey($validated['curriculum_subject_id'])
            ->where('curriculum_id', $academicClass->curriculum_id)
            ->where('is_active', true)
            ->first();

        if (!$subject) {
            return back()
                ->withInput()
                ->with('error', 'Disciplina inválida para esta turma.');
        }

        if (
            $validated['status'] === 'cancelled' &&
            $session->attendanceRecords()->exists()
        ) {
            return back()
                ->withInput()
                ->with('error', 'Não é possível cancelar uma aula que já possui frequência registada.');
        }

        $session->update([
            'curriculum_subject_id' => $subject->id,
            'session_date' => $validated['session_date'],
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'topic' => $validated['topic'] ?? null,
            'content' => $validated['content'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()
            ->route('university.attendance.sessions.index', $academicClass)
            ->with('success', 'Aula atualizada com sucesso.');
    }

    public function destroy(string $session)
    {
        $session = ClassSession::findOrFail($session); 

        if ($session->attendanceRecords()->exists()) {
            return back()->with(
                'error',
                'Não é possível eliminar uma aula que já possui frequência registada.'
            );
        }

        $academicClass = $session->academicClass;

        $session->delete();

        return redirect()
            ->route('university.attendance.sessions.index', $academicClass)
            ->with('success', 'Aula eliminada com sucesso.');
    }
}
