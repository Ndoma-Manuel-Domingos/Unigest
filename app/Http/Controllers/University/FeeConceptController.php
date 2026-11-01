<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\FeeConcept;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FeeConceptController extends Controller
{
    public function index(Request $request)
    {
        $query = FeeConcept::query()
            ->withCount(['tariffs', 'invoiceItems']);

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('code', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where(
                'is_active',
                $request->status === 'active'
            );
        }

        if ($request->filled('recurring')) {
            $query->where(
                'is_recurring',
                $request->recurring === 'yes'
            );
        }

        $concepts = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('university.finance.fee-concepts.index', compact('concepts'));
    }

    public function create()
    {
        return view('university.finance.fee-concepts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('fee_concepts', 'code')
                    ->where('tenant_id', auth()->user()->tenant_id),
            ],
            'description' => ['nullable', 'string'],
            'is_recurring' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_recurring'] = $request->boolean('is_recurring');
        $validated['is_active'] = $request->boolean('is_active');

        FeeConcept::create($validated);

        return redirect()
            ->route('university.fee-concepts.index')
            ->with('success', 'Conceito de cobrança criado com sucesso.');
    }

    public function edit(string $feeConcept)
    {

        $feeConcept = FeeConcept::findOrFail($feeConcept); 

        return view(
            'university.finance.fee-concepts.edit',
            compact('feeConcept')
        );
    }

    public function update(Request $request, string $feeConcept)
    {
        
        $feeConcept = FeeConcept::findOrFail($feeConcept); 

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('fee_concepts', 'code')
                    ->where('tenant_id', $feeConcept->tenant_id)
                    ->ignore($feeConcept->id),
            ],
            'description' => ['nullable', 'string'],
            'is_recurring' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_recurring'] = $request->boolean('is_recurring');
        $validated['is_active'] = $request->boolean('is_active');

        $feeConcept->update($validated);

        return redirect()
            ->route('university.fee-concepts.index')
            ->with('success', 'Conceito de cobrança actualizado com sucesso.');
    }

    public function toggleStatus(FeeConcept $feeConcept)
    {
        $feeConcept->update([
            'is_active' => !$feeConcept->is_active,
        ]);

        return back()->with(
            'success',
            $feeConcept->is_active
                ? 'Conceito activado com sucesso.'
                : 'Conceito desactivado com sucesso.'
        );
    }

    public function destroy(string $feeConcept)
    {
        $feeConcept = FeeConcept::findOrFail($feeConcept); 

        if (
            $feeConcept->tariffs()->exists() ||
            $feeConcept->invoiceItems()->exists()
        ) {
            return back()->with(
                'error',
                'Não é possível eliminar este conceito porque já está associado a tarifas ou facturas. Desactive-o em vez disso.'
            );
        }

        $feeConcept->delete();

        return redirect()
            ->route('university.fee-concepts.index')
            ->with('success', 'Conceito eliminado com sucesso.');
    }
}
