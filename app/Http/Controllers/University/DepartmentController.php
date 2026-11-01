<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::query()
            ->with('faculty')
            ->withCount('faculty')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('acronym', 'LIKE', "%{$search}%")
                        ->orWhereHas('faculty', function ($faculty) use ($search) {
                            $faculty->where('name', 'LIKE', "%{$search}%");
                        });
                });
            })
            ->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->when($request->filled('faculty_id'), function ($query) use ($request) {
                $query->where('faculty_id', $request->faculty_id);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $faculties = Faculty::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('university.departments.index', compact(
            'departments',
            'faculties'
        ));
    }

    public function create()
    {
        $faculties = Faculty::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('university.departments.create', compact('faculties'));
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $data = $request->validate([
            'faculty_id' => [
                'required',
                Rule::exists('faculties', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('is_active', true),
            ],
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('departments', 'name')
                    ->where('tenant_id', $tenantId)
                    ->where('faculty_id', $request->faculty_id),
            ],
            'acronym' => 'nullable|string|max:30',
            'description' => 'nullable|string',
        ]);

        Department::create([
            ...$data,
            'is_active' => true,
        ]);

        return redirect()
            ->route('university.departments.index')
            ->with('success', 'Departamento cadastrado com sucesso.');
    }

    public function edit(int $id)
    {
        $department = Department::findOrFail($id);

        $faculties = Faculty::where('is_active', true)
            ->orWhere('id', $department->faculty_id)
            ->orderBy('name')
            ->get();

        return view('university.departments.edit', compact(
            'department',
            'faculties'
        ));
    }

    public function update(Request $request, int $id)
    {
        $department = Department::findOrFail($id);
        $tenantId = auth()->user()->tenant_id;

        $data = $request->validate([
            'faculty_id' => [
                'required',
                Rule::exists('faculties', 'id')
                    ->where('tenant_id', $tenantId),
            ],
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('departments', 'name')
                    ->where('tenant_id', $tenantId)
                    ->where('faculty_id', $request->faculty_id)
                    ->ignore($department->id),
            ],
            'acronym' => 'nullable|string|max:30',
            'description' => 'nullable|string',
        ]);

        $department->update($data);

        return redirect()
            ->route('university.departments.index')
            ->with('success', 'Departamento actualizado com sucesso.');
    }

    public function updateStatus(int $id)
    {
        $department = Department::findOrFail($id);

        $department->update([
            'is_active' => !$department->is_active,
        ]);

        return back()->with('success', 'Estado actualizado com sucesso.');
    }

    public function destroy(int $id)
    {
        $department = Department::findOrFail($id);

        $department->delete();

        return redirect()
            ->route('university.departments.index')
            ->with('success', 'Departamento eliminado com sucesso.');
    }
}
