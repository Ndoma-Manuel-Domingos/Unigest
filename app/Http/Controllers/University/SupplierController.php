<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%")
                        ->orWhere('tax_number', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where(
                    'is_active',
                    $request->status === 'active'
                );
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('university.finance.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('university.finance.suppliers.create');
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('suppliers', 'code')
                    ->where('tenant_id', $tenantId),
            ],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:100'],
            'iban' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        Supplier::create($validated);

        return redirect()
            ->route('university.suppliers.index')
            ->with('success', 'Fornecedor registado com sucesso.');
    }

    public function show(string $supplier)
    {

        $supplier = Supplier::findOrFail($supplier); 

        $supplier->load([
            'expenses' => function ($query) {
                $query->latest('expense_date')->limit(10);
            },
        ]);

        return view('university.finance.suppliers.show', compact('supplier'));
    }

    public function edit(string $supplier)
    {
        $supplier = Supplier::findOrFail($supplier);

        return view('university.finance.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, string $supplier)
    {
        $tenantId = auth()->user()->tenant_id;

        $supplier = Supplier::findOrFail($supplier);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('suppliers', 'code')
                    ->where('tenant_id', $tenantId)
                    ->ignore($supplier->id),
            ],
            'tax_number' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:100'],
            'iban' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $supplier->update($validated);

        return redirect()
            ->route('university.suppliers.index')
            ->with('success', 'Fornecedor actualizado com sucesso.');
    }

    public function toggleStatus(string $supplier)
    {
        $supplier = Supplier::findOrFail($supplier);

        $supplier->update([
            'is_active' => !$supplier->is_active,
        ]);

        return back()->with(
            'success',
            $supplier->is_active
                ? 'Fornecedor activado com sucesso.'
                : 'Fornecedor desactivado com sucesso.'
        );
    }
}
