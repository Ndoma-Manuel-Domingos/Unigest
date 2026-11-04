<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $students = Student::query()
            ->withCount('enrollments')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('student_number', 'ILIKE', "%{$search}%")
                        ->orWhere('nif', 'ILIKE', "%{$search}%")
                        ->orWhere('phone', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->status === 'active');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('university.students.index', compact('students'));
    }

    public function create()
    {
        return view('university.students.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $student = DB::transaction(function () use ($data, $request) {
            $tenantId = auth()->user()->tenant_id;

            // Serializa a geração do número por universidade.
            University::whereKey($tenantId)->lockForUpdate()->firstOrFail();

            $nextNumber = Student::query()->count() + 1;

            $data['student_number'] = sprintf(
                'EST-%s-%06d',
                now()->format('Y'),
                $nextNumber
            );

            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')
                    ->store('students', 'public');
            }

            return Student::create($data);
        });

        return redirect()
            ->route('university.students.show', $student)
            ->with('success', 'Estudante registado com sucesso.');
    }

    public function show(string $id)
    {
        $student = Student::findOrFail($id);

        $student->load([
            'enrollments.academicYear',
            'enrollments.course',
            'enrollments.academicClass',
        ]);

        return view('university.students.show', compact('student'));
    }

    public function edit(string $id)
    {

        $student = Student::findOrFail($id);

        return view('university.students.edit', compact('student'));
    }

    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);
        
        $data = $this->validateData($request, $student);

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }

            $data['photo'] = $request->file('photo')
                ->store('students', 'public');
        }

        $student->update($data);

        return redirect()
            ->route('university.students.show', $student)
            ->with('success', 'Dados do estudante actualizados.');
    }

    public function updateStatus(Student $student)
    {
        $student->update([
            'is_active' => !$student->is_active,
        ]);

        return back()->with('success', 'Estado do estudante actualizado.');
    }

    private function validateData(
        Request $request,
        ?Student $student = null
    ): array {
        $tenantId = auth()->user()->tenant_id;

        return $request->validate([
            'nif' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('students', 'nif')
                    ->where('tenant_id', $tenantId)
                    ->ignore($student?->id),
            ],

            'document_type' => ['nullable', 'string', 'max:30'],

            'document_number' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('students', 'document_number')
                    ->where('tenant_id', $tenantId)
                    ->where('document_type', $request->input('document_type'))
                    ->ignore($student?->id),
            ],

            'name' => ['required', 'string', 'max:200'],
            'gender' => ['nullable', Rule::in(['masculino', 'feminino', 'outro'])],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'birth_place' => ['nullable', 'string', 'max:150'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'marital_status' => [
                'nullable',
                Rule::in(['solteiro', 'casado', 'divorciado', 'viuvo', 'outro']),
            ],

            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'alternative_phone' => ['nullable', 'string', 'max:30'],

            'province' => ['nullable', 'string', 'max:100'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:255'],

            'guardian_name' => ['nullable', 'string', 'max:200'],
            'guardian_phone' => ['nullable', 'string', 'max:30'],
            'guardian_relationship' => ['nullable', 'string', 'max:50'],

            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'observations' => ['nullable', 'string', 'max:5000'],
            'is_active' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => $request->boolean(
                'is_active',
                $student?->is_active ?? true
            ),
        ];
    }
}
