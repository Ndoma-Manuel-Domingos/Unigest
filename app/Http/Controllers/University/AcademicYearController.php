<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $years = AcademicYear::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->status === 'active');
            })
            ->when($request->filled('current'), function ($query) use ($request) {
                $query->where('is_current', $request->current === 'yes');
            })
            ->withCount('classes')
            ->orderByDesc('start_date')
            ->paginate(15)
            ->withQueryString();

        return view('university.academic-years.index', compact('years'));
    }

    public function create()
    {
        return view('university.academic-years.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($data) {
            $isCurrent = $data['is_current'] ?? false;

            if ($isCurrent) {
                AcademicYear::query()->update(['is_current' => false]);
            }

            AcademicYear::create($data);
        });

        return redirect()
            ->route('university.academic-years.index')
            ->with('success', 'Ano lectivo registado com sucesso.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('university.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $data = $this->validateData($request);

        DB::transaction(function () use ($data, $academicYear) {
            $isCurrent = $data['is_current'] ?? false;

            if ($isCurrent) {
                AcademicYear::query()
                    ->whereKeyNot($academicYear->id)
                    ->update(['is_current' => false]);
            }

            $academicYear->update($data);
        });

        return redirect()
            ->route('university.academic-years.index')
            ->with('success', 'Ano lectivo actualizado com sucesso.');
    }

    public function updateStatus(AcademicYear $academicYear)
    {
        $academicYear->update([
            'is_active' => !$academicYear->is_active,
        ]);

        return back()->with('success', 'Estado actualizado com sucesso.');
    }

    public function setCurrent(AcademicYear $academicYear)
    {
        abort_unless($academicYear->is_active, 422, 'Active o ano lectivo antes de o definir como actual.');

        DB::transaction(function () use ($academicYear) {
            AcademicYear::query()->update(['is_current' => false]);

            $academicYear->update(['is_current' => true]);
        });

        return back()->with('success', 'Ano lectivo actual definido com sucesso.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->classes()->exists()) {
            return back()->with('error', 'Não é possível eliminar este ano lectivo porque possui turmas associadas.');
        }

        $academicYear->delete();

        return redirect()
            ->route('university.academic-years.index')
            ->with('success', 'Ano lectivo eliminado com sucesso.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('academic_years', 'name')
                    ->where('tenant_id', auth()->user()->tenant_id)
                    ->ignore($request->route('academicYear')?->id),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
            'is_current' => ['nullable', 'boolean'],
        ]) + [
            'is_active' => $request->boolean('is_active'),
            'is_current' => $request->boolean('is_current'),
        ];
    }
}
