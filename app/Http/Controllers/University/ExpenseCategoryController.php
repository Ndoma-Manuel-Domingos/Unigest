<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = ExpenseCategory::query()
            ->withCount('expenses')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('code', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('is_active', $request->status === 'active');
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view(
            'university.finance.expense-categories.index',
            compact('categories')
        );
    }

    public function create()
    {
        return view('university.finance.expense-categories.create');
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
                Rule::unique('expense_categories', 'code')
                    ->where('tenant_id', $tenantId),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        ExpenseCategory::create($validated);

        return redirect()
            ->route('university.expense-categories.index')
            ->with('success', 'Categoria de despesa criada com sucesso.');
    }

    public function show(string $expenseCategory)
    {
        $expenseCategory = ExpenseCategory::findOrFail($expenseCategory); 

        $expenseCategory->load([
            'expenses' => function ($query) {
                $query->latest('expense_date')->limit(15);
            },
        ]);

        return view(
            'university.finance.expense-categories.show',
            compact('expenseCategory')
        );
    }

    public function edit(string $expenseCategory)
    {
        $expenseCategory = ExpenseCategory::findOrFail($expenseCategory); 

        return view(
            'university.finance.expense-categories.edit',
            compact('expenseCategory')
        );
    }

    public function update(Request $request, string $expenseCategory)
    {
        $tenantId = auth()->user()->tenant_id;

        $expenseCategory = ExpenseCategory::findOrFail($expenseCategory); 

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('expense_categories', 'code')
                    ->where('tenant_id', $tenantId)
                    ->ignore($expenseCategory->id),
            ],
            'description' => ['nullable', 'string'],
        ]);

        $expenseCategory->update($validated);

        return redirect()
            ->route('university.expense-categories.index')
            ->with('success', 'Categoria actualizada com sucesso.');
    }

    public function toggleStatus(string $expenseCategory)
    {
        $expenseCategory = ExpenseCategory::findOrFail($expenseCategory); 

        $expenseCategory->update([
            'is_active' => !$expenseCategory->is_active,
        ]);

        return back()->with(
            'success',
            $expenseCategory->is_active
                ? 'Categoria activada com sucesso.'
                : 'Categoria desactivada com sucesso.'
        );
    }
}
