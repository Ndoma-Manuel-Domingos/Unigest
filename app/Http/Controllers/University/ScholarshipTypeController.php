<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ScholarshipTypeController extends Controller
{
    public function index(Request $request)
    {
        $scholarships = ScholarshipType::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('code', 'ILIKE', "%{$search}%");
                });
            })
            ->when($request->filled('type'), fn ($q) =>
                $q->where('type', $request->type)
            )
            ->when($request->filled('status'), fn ($q) =>
                $q->where('is_active', $request->status === 'active')
            )
            ->withCount('studentScholarships')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('university.scholarships.types.index', compact('scholarships'));
    }

    public function create()
    {
        return view('university.scholarships.types.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        ScholarshipType::create($data);

        return redirect()
            ->route('university.scholarship-types.index')
            ->with('success', 'Tipo de bolsa criado com sucesso.');
    }

    public function show(string $scholarshipType)
    {
        $scholarshipType = ScholarshipType::findOrFail($scholarshipType);

        $scholarshipType->load([
            'studentScholarships.student',
            'studentScholarships.academicYear',
        ]);

        return view(
            'university.scholarships.types.show',
            compact('scholarshipType')
        );
    }

    public function edit(string $scholarshipType)
    {
        $scholarshipType = ScholarshipType::findOrFail($scholarshipType);

        return view(
            'university.scholarships.types.edit',
            compact('scholarshipType')
        );
    }

    public function update(Request $request, string $scholarshipType)
    {
        $scholarshipType = ScholarshipType::findOrFail($scholarshipType);

        $data = $this->validateData($request, $scholarshipType);

        $scholarshipType->update($data);

        return redirect()
            ->route('university.scholarship-types.index')
            ->with('success', 'Tipo de bolsa atualizado com sucesso.');
    }

    public function destroy(string $scholarshipType)
    {
        $scholarshipType = ScholarshipType::findOrFail($scholarshipType);

        if ($scholarshipType->studentScholarships()->exists()) {
            return back()->with(
                'error',
                'Não é possível eliminar este tipo de bolsa porque já está atribuído a estudantes.'
            );
        }

        $scholarshipType->delete();

        return redirect()
            ->route('university.scholarship-types.index')
            ->with('success', 'Tipo de bolsa eliminado com sucesso.');
    }

    private function validateData(
        Request $request,
        ?ScholarshipType $scholarshipType = null
    ): array {

        $tenantId = auth()->user()->tenant_id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('scholarship_types', 'code')
                    ->where('tenant_id', $tenantId)
                    ->ignore($scholarshipType?->id),
            ],
            'description' => ['nullable', 'string'],
            'type' => ['required', Rule::in(['percentage', 'fixed'])],
            'value' => ['required', 'numeric', 'gt:0'],
            'maximum_amount' => ['nullable', 'numeric', 'gte:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($data['type'] === 'percentage' && $data['value'] > 100) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'value' => 'A percentagem não pode ser superior a 100%.',
            ]);
        }

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
