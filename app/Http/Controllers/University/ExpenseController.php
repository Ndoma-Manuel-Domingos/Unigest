<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $expenses = Expense::query()
            ->with(['supplier', 'category', 'creator'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('expense_number', 'LIKE', "%{$search}%")
                        ->orWhere('description', 'LIKE', "%{$search}%")
                        ->orWhere('reference', 'LIKE', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            })
            ->when($request->filled('supplier_id'), function ($query) use ($request) {
                $query->where('supplier_id', $request->supplier_id);
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('expense_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('expense_date', '<=', $request->date_to);
            })
            ->latest('expense_date')
            ->latest('id')
            ->paginate(20)
            ->withQueryString();

        $categories = ExpenseCategory::active()->orderBy('name')->get();
        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('university.finance.expenses.index', compact(
            'expenses',
            'categories',
            'suppliers'
        ));
    }

    public function create()
    {
        $categories = ExpenseCategory::active()
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::active()
            ->orderBy('name')
            ->get();

        return view('university.finance.expenses.create', compact(
            'categories',
            'suppliers'
        ));
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'supplier_id' => [
                'nullable',
                Rule::exists('suppliers', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('is_active', true),
            ],
            'category_id' => [
                'required',
                Rule::exists('expense_categories', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('is_active', true),
            ],
            'description' => ['required', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:100'],
            'expense_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:expense_date'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'currency' => ['required', 'string', 'size:3'],
            'notes' => ['nullable', 'string'],
        ]);

        $expense = DB::transaction(function () use ($validated, $tenantId) {
            // Serializa a geração do número por universidade.
            DB::table('universities')
                ->where('id', $tenantId)
                ->lockForUpdate()
                ->first();

            $year = now()->format('Y');
            $prefix = "DES-{$year}-";

            $lastExpense = Expense::query()
                ->where('expense_number', 'LIKE', "{$prefix}%")
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $lastNumber = 0;

            if ($lastExpense && preg_match('/(\d+)$/', $lastExpense->expense_number, $matches)) {
                $lastNumber = (int) $matches[1];
            }

            $expenseNumber = $prefix . str_pad(
                $lastNumber + 1,
                6,
                '0',
                STR_PAD_LEFT
            );

            return Expense::create([
                ...$validated,
                'expense_number' => $expenseNumber,
                'created_by' => auth()->id(),
                'amount' => $validated['amount'],
                'paid_amount' => 0,
                'balance' => $validated['amount'],
                'status' => 'draft',
                'currency' => strtoupper($validated['currency']),
            ]);
        });

        return redirect()
            ->route('university.expenses.show', $expense)
            ->with('success', 'Despesa registada como rascunho com sucesso.');
    }

    public function show(string $expense)
    {
        $expense = Expense::findOrFail($expense); 

        $expense->load([
            'supplier',
            'category',
            'creator',
            'approver',
            'payments.user',
        ]);

        return view('university.finance.expenses.show', compact('expense'));
    }

    public function edit(string $expense)
    {
        $expense = Expense::findOrFail($expense); 

        abort_unless($expense->status === 'draft', 403,
            'Apenas despesas em rascunho podem ser editadas.');

        $categories = ExpenseCategory::active()
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::active()
            ->orderBy('name')
            ->get();

        return view('university.finance.expenses.edit', compact(
            'expense',
            'categories',
            'suppliers'
        ));
    }

    public function update(Request $request, string $expense)
    {
        $expense = Expense::findOrFail($expense); 

        abort_unless($expense->status === 'draft', 403,
            'Apenas despesas em rascunho podem ser editadas.');

        $tenantId = auth()->user()->tenant_id;

        $validated = $request->validate([
            'supplier_id' => [
                'nullable',
                Rule::exists('suppliers', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('is_active', true),
            ],
            'category_id' => [
                'required',
                Rule::exists('expense_categories', 'id')
                    ->where('tenant_id', $tenantId)
                    ->where('is_active', true),
            ],
            'description' => ['required', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:100'],
            'expense_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:expense_date'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
            'currency' => ['required', 'string', 'size:3'],
            'notes' => ['nullable', 'string'],
        ]);

        $expense->fill([
            ...$validated,
            'currency' => strtoupper($validated['currency']),
            'balance' => $validated['amount'],
        ]);

        $expense->save();

        return redirect()
            ->route('university.expenses.show', $expense)
            ->with('success', 'Despesa actualizada com sucesso.');
    }

    public function approve(string $expense)
    {
        $expense = Expense::findOrFail($expense); 

        abort_unless($expense->canBeApproved(), 422,
            'Esta despesa não pode ser aprovada no estado actual.');

        $expense->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Despesa aprovada com sucesso.');
    }

    public function cancel(string $expense)
    {

        $expense = Expense::findOrFail($expense); 

        abort_unless($expense->canBeCancelled(), 422,
            'Esta despesa não pode ser cancelada. Verifica o estado e os pagamentos.');

        $expense->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Despesa cancelada com sucesso.');
    }
}
