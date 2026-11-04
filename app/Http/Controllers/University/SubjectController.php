<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $subjects = Subject::query()
            ->with('department')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('code', 'ILIKE', "%{$search}%");
                });
            })
            ->when($request->filled('department_id'), function ($query) use ($request) {
                $query->where('department_id', $request->department_id);
            })
            ->when($request->filled('type'), function ($query) use ($request) {
                $query->where('type', $request->type);
            })
            ->when($request->filled('year_number'), function ($query) use ($request) {
                $query->where('year_number', $request->year_number);
            })
            ->when($request->filled('semester_number'), function ($query) use ($request) {
                $query->where('semester_number', $request->semester_number);
            })
            ->when(
                $request->status !== null &&
                $request->status !== '',
                function ($query) use ($request) {
                    $query->where('is_active', $request->status);
                }
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'university.subjects.index',
            compact('subjects', 'departments')
        );
    }

    public function create()
    {
        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'university.subjects.create',
            compact('departments')
        );
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        Subject::create($data);

        return redirect()
            ->route('university.subjects.index')
            ->with('success', 'Disciplina cadastrada com sucesso.');
    }

    public function edit(int $id)
    {
        $subject = Subject::findOrFail($id);

        $departments = Department::query()
            ->where('is_active', true)
            ->orWhere('id', $subject->department_id)
            ->orderBy('name')
            ->get();

        return view(
            'university.subjects.edit',
            compact('subject', 'departments')
        );
    }

    public function update(Request $request, int $id)
    {
        $subject = Subject::findOrFail($id);

        $data = $this->validateData(
            $request,
            $subject->id
        );

        $subject->update($data);

        return redirect()
            ->route('university.subjects.index')
            ->with('success', 'Disciplina actualizada com sucesso.');
    }

    public function updateStatus(int $id)
    {
        $subject = Subject::findOrFail($id);

        $subject->update([
            'is_active' => !$subject->is_active,
        ]);

        return back()
            ->with('success', 'Estado actualizado com sucesso.');
    }

    public function destroy(int $id)
    {
        $subject = Subject::findOrFail($id);

        if ($subject->is_active) {
            return back()->withErrors([
                'subject' => 'Desactive a disciplina antes de a eliminar.',
            ]);
        }

        $subject->delete();

        return redirect()
            ->route('university.subjects.index')
            ->with('success', 'Disciplina eliminada com sucesso.');
    }

    private function validateData(
        Request $request,
        ?int $subjectId = null
    ): array {
        $tenantId = auth()->user()->tenant_id;

        $data = $request->validate([
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
                Rule::unique('subjects', 'name')
                    ->where('tenant_id', $tenantId)
                    ->ignore($subjectId),
            ],

            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('subjects', 'code')
                    ->where('tenant_id', $tenantId)
                    ->ignore($subjectId),
            ],

            'description' => 'nullable|string',

            'type' => [
                'required',
                Rule::in([
                    'obrigatoria',
                    'opcional',
                    'electiva',
                    'livre',
                ]),
            ],

            'year_number' => 'nullable|integer|min:1|max:10',

            'semester_number' => 'nullable|integer|min:1|max:4',

            'credits' => 'required|integer|min:0|max:100',

            'theoretical_hours' => 'required|integer|min:0|max:2000',

            'practical_hours' => 'required|integer|min:0|max:2000',

            'laboratory_hours' => 'required|integer|min:0|max:2000',

            'requires_prerequisite' => 'nullable|boolean',
        ]);

        $data['requires_prerequisite'] =
            $request->boolean('requires_prerequisite');

        $data['total_hours'] =
            (int) $data['theoretical_hours']
            + (int) $data['practical_hours']
            + (int) $data['laboratory_hours'];

        $data['is_active'] = true;

        return $data;
    }
}
