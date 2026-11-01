<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FacultyController extends Controller
{
    public function index(Request $request)
    {
        $faculties = Faculty::query()
            ->withCount('departments')
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('acronym', 'LIKE', "%{$search}%");
                });
            })
            ->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
                $query->where('is_active', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('university.faculties.index', compact('faculties'));
    }

    public function create()
    {
        return view('university.faculties.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('faculties', 'name')
                    ->where('tenant_id', auth()->user()->tenant_id),
            ],
            'acronym' => 'nullable|string|max:30',
            'description' => 'nullable|string',
        ]);

        Faculty::create([
            ...$data,
            'is_active' => true,
        ]);

        return redirect()
            ->route('university.faculties.index')
            ->with('success', 'Faculdade cadastrada com sucesso.');
    }

    public function edit(int $id)
    {
        $faculty = Faculty::findOrFail($id);

        return view('university.faculties.edit', compact('faculty'));
    }

    public function update(Request $request, int $id)
    {
        $faculty = Faculty::findOrFail($id);

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('faculties', 'name')
                    ->where('tenant_id', auth()->user()->tenant_id)
                    ->ignore($faculty->id),
            ],
            'acronym' => 'nullable|string|max:30',
            'description' => 'nullable|string',
        ]);

        $faculty->update($data);

        return redirect()
            ->route('university.faculties.index')
            ->with('success', 'Faculdade actualizada com sucesso.');
    }

    public function updateStatus(int $id)
    {
        $faculty = Faculty::findOrFail($id);

        $faculty->update([
            'is_active' => !$faculty->is_active,
        ]);

        return back()->with('success', 'Estado actualizado com sucesso.');
    }

    public function destroy(int $id)
    {
        $faculty = Faculty::findOrFail($id);

        if ($faculty->departments()->exists()) {
            return back()->withErrors([
                'faculty' => 'Não é possível eliminar esta faculdade porque possui departamentos associados.',
            ]);
        }

        $faculty->delete();

        return redirect()
            ->route('university.faculties.index')
            ->with('success', 'Faculdade eliminada com sucesso.');
    }
}
