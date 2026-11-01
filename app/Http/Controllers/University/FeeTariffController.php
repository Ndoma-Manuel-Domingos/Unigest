<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Course;
use App\Models\FeeConcept;
use App\Models\FeeTariff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeeTariffController extends Controller
{
    private function formData(): array
    {
        return [
            'concepts' => FeeConcept::where('is_active', true)
                ->orderBy('name')->get(),

            'academicYears' => AcademicYear::where('is_active', true)
                ->orderByDesc('start_date')->get(),

            'courses' => Course::where('is_active', true)
                ->orderBy('name')->get(),
        ];
    }

    public function index(Request $request)
    {
        $tariffs = FeeTariff::with(['feeConcept', 'academicYear', 'course'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->whereHas('feeConcept', function ($sub) use ($search) {
                        $sub->where('name', 'LIKE', "%{$search}%");
                    })->orWhereHas('course', function ($sub) use ($search) {
                        $sub->where('name', 'LIKE', "%{$search}%");
                    });
                });
            })
            ->when($request->filled('academic_year_id'), fn ($q) =>
                $q->where('academic_year_id', $request->academic_year_id)
            )
            ->when($request->filled('fee_concept_id'), fn ($q) =>
                $q->where('fee_concept_id', $request->fee_concept_id)
            )
            ->when($request->filled('status'), fn ($q) =>
                $q->where('is_active', $request->status === 'active')
            )
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('university.finance.fee-tariffs.index', [
            'tariffs' => $tariffs,
            'academicYears' => AcademicYear::orderByDesc('start_date')->get(),
            'concepts' => FeeConcept::orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view(
            'university.finance.fee-tariffs.create',
            $this->formData()
        );
    }

    public function store(Request $request)
    {
        $data = $this->validateTariff($request);

        $this->ensureNoDuplicate($data);

        FeeTariff::create($data);

        return redirect()
            ->route('university.fee-tariffs.index')
            ->with('success', 'Tarifa registada com sucesso.');
    }

    public function edit(string $feeTariff)
    {
        $feeTariff = FeeTariff::findOrFail($feeTariff);

        return view(
            'university.finance.fee-tariffs.edit',
            array_merge($this->formData(), ['feeTariff' => $feeTariff])
        );
    }

    public function update(Request $request, string $feeTariff)
    {
        $feeTariff = FeeTariff::findOrFail($feeTariff);

        $data = $this->validateTariff($request);

        $this->ensureNoDuplicate($data, $feeTariff->id);

        $feeTariff->update($data);

        return redirect()
            ->route('university.fee-tariffs.index')
            ->with('success', 'Tarifa actualizada com sucesso.');
    }

    public function toggleStatus(string $feeTariff)
    {

        $feeTariff = FeeTariff::findOrFail($feeTariff);

        $feeTariff->update([
            'is_active' => ! $feeTariff->is_active,
        ]);

        return back()->with('success', 'Estado da tarifa actualizado.');
    }

    public function destroy(string $feeTariff)
    {
        $feeTariff = FeeTariff::findOrFail($feeTariff);

        if ($feeTariff->is_active) {
            return back()->with(
                'error',
                'Desactive a tarifa antes de a eliminar.'
            );
        }

        $feeTariff->delete();

        return back()->with('success', 'Tarifa eliminada com sucesso.');
    }

    private function validateTariff(Request $request): array
    {
        $data = $request->validate([
            'fee_concept_id' => [
                'required',
                'integer',
                Rule::exists('fee_concepts', 'id')
                    ->where('tenant_id', auth()->user()->tenant_id),
            ],
            'academic_year_id' => [
                'required',
                'integer',
                Rule::exists('academic_years', 'id')
                    ->where('tenant_id', auth()->user()->tenant_id),
            ],
            'course_id' => [
                'nullable',
                'integer',
                Rule::exists('courses', 'id')
                    ->where('tenant_id', auth()->user()->tenant_id),
            ],
            'amount' => ['required', 'numeric', 'gt:0', 'max:999999999999.99'],
            'frequency' => [
                'required',
                Rule::in(['once', 'monthly', 'semesterly', 'annually']),
            ],
            'due_day' => [
                'nullable',
                'integer',
                'between:1,28',
                'required_if:frequency,monthly',
            ],
            'currency' => ['required', Rule::in(['AOA', 'USD', 'EUR'])],
            'description' => ['nullable', 'string', 'max:1000'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'fee_concept_id.required' => 'Seleccione o conceito de cobrança.',
            'academic_year_id.required' => 'Seleccione o ano académico.',
            'amount.gt' => 'O valor deve ser superior a zero.',
            'due_day.required_if' => 'Indique o dia de vencimento para tarifas mensais.',
        ]);

        $concept = FeeConcept::findOrFail($data['fee_concept_id']);

        if (! $concept->is_active) {
            abort(422, 'O conceito de cobrança seleccionado está inactivo.');
        }

        AcademicYear::findOrFail($data['academic_year_id']);

        if (! empty($data['course_id'])) {
            Course::findOrFail($data['course_id']);
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['due_day'] = $data['frequency'] === 'monthly'
            ? $data['due_day']
            : null;

        return $data;
    }

    private function ensureNoDuplicate(array $data, ?int $ignoreId = null): void
    {
        $query = FeeTariff::query()
            ->where('fee_concept_id', $data['fee_concept_id'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('frequency', $data['frequency'])
            ->where('currency', $data['currency']);

        if (empty($data['course_id'])) {
            $query->whereNull('course_id');
        } else {
            $query->where('course_id', $data['course_id']);
        }

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($query->exists()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'fee_concept_id' => 'Já existe uma tarifa com este conceito, ano, curso, periodicidade e moeda.',
            ]);
        }
    }
}
