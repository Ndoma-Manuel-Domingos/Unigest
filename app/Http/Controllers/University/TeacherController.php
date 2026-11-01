<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $teachers = Teacher::query()
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {
                    $search = $request->search;

                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', "%{$search}%")
                            ->orWhere(
                                'teacher_number',
                                'LIKE',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'employee_number',
                                'LIKE',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'email',
                                'LIKE',
                                "%{$search}%"
                            );
                    });
                }
            )
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view(
            'university.teachers.index',
            compact('teachers')
        );
    }

    public function create()
    {
        return view('university.teachers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'employee_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'gender' => [
                'nullable',
                'in:masculino,feminino,outro',
            ],

            'birth_date' => [
                'nullable',
                'date',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'academic_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'specialization' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'observations' => [
                'nullable',
                'string',
            ],
        ]);

        $year = now()->format('Y');

        $last = Teacher::query()
            ->where('teacher_number', 'like', "DOC-{$year}-%")
            ->lockForUpdate()
            ->orderByDesc('id')
            ->first();

        $number = $last
            ? ((int) substr($last->teacher_number, -6)) + 1
            : 1;

        $validated['teacher_number'] =
            'DOC-' . $year . '-' . str_pad(
                $number,
                6,
                '0',
                STR_PAD_LEFT
            );

        $validated['is_active'] = true;

        Teacher::create($validated);

        return redirect()
            ->route('university.teachers.index')
            ->with('success', 'Professor criado com sucesso.');
    }

    public function edit(string $teacher)
    {

        $teacher = Teacher::findOrFail($teacher);
        
        return view(
            'university.teachers.edit',
            compact('teacher')
        );
    }

    public function update(
        Request $request,
        string $teacher
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'employee_number' => [
                'nullable',
                'string',
                'max:100',
            ],

            'gender' => [
                'nullable',
                'in:masculino,feminino,outro',
            ],

            'birth_date' => [
                'nullable',
                'date',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'academic_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'specialization' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'observations' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $teacher = Teacher::findOrFail($teacher);

        $teacher->update($validated);

        return redirect()
            ->route('university.teachers.index')
            ->with('success', 'Professor actualizado com sucesso.');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->schedules()->exists()) {
            return back()->with(
                'error',
                'Não é possível eliminar um professor que possui horários.'
            );
        }

        $teacher->delete();

        return redirect()
            ->route('university.teachers.index')
            ->with('success', 'Professor eliminado com sucesso.');
    }
}
