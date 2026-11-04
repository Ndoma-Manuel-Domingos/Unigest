<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::query()
            ->withCount('movements')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->paginate(15);

        return view(
            'university.finance.bank-accounts.index',
            compact('accounts')
        );
    }

    public function create()
    {
        return view(
            'university.finance.bank-accounts.create'
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'bank_name' => ['required', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'iban' => ['nullable', 'string', 'max:100'],
            'swift' => ['nullable', 'string', 'max:50'],
            'currency' => ['required', 'string', 'size:3'],
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'branch' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        DB::transaction(function () use ($data) {

            $data['is_active'] = $data['is_active'] ?? false;
            $data['is_default'] = $data['is_default'] ?? false;

            if ($data['is_default']) {
                BankAccount::query()
                    ->update(['is_default' => false]);
            }

            $data['current_balance'] = $data['opening_balance'];

            BankAccount::create($data);
        });

        return redirect()
            ->route('university.bank-accounts.index')
            ->with('success', 'Conta bancária criada com sucesso.');
    }

    public function show(string $bankAccount)
    {
        $bankAccount = BankAccount::findOrFail($bankAccount);

        $bankAccount->load([
            'movements' => function ($query) {
                $query
                    ->latest('movement_date')
                    ->latest('id')
                    ->limit(20);
            },
        ]);

        $movementsCount = BankMovement::query()
            ->where('bank_account_id', $bankAccount->id)
            ->count();

        return view(
            'university.finance.bank-accounts.show',
            compact('bankAccount', 'movementsCount')
        );
    }

    public function edit(string $bankAccount)
    {
        $bankAccount = BankAccount::findOrFail($bankAccount);

        return view(
            'university.finance.bank-accounts.edit',
            compact('bankAccount')
        );
    }

    public function update(
        Request $request,
        string $bankAccount
    ) {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'bank_name' => ['required', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:100'],
            'iban' => ['nullable', 'string', 'max:100'],
            'swift' => ['nullable', 'string', 'max:50'],
            'currency' => ['required', 'string', 'size:3'],
            'branch' => ['nullable', 'string', 'max:150'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $bankAccount = BankAccount::findOrFail($bankAccount);

        DB::transaction(function () use ($data, $bankAccount) {

            $data['is_active'] = $data['is_active'] ?? false;
            $data['is_default'] = $data['is_default'] ?? false;

            if ($data['is_default']) {
                BankAccount::query()
                    ->where('id', '!=', $bankAccount->id)
                    ->update(['is_default' => false]);
            }

            $bankAccount->update($data);
        });

        return redirect()
            ->route('university.bank-accounts.index')
            ->with('success', 'Conta bancária atualizada com sucesso.');
    }

    public function destroy(string $bankAccount)
    {
        $bankAccount = BankAccount::findOrFail($bankAccount);

        if ($bankAccount->movements()->exists()) {
            return back()->with(
                'error',
                'Não é possível eliminar uma conta bancária que possui movimentos.'
            );
        }

        $bankAccount->delete();

        return redirect()
            ->route('university.bank-accounts.index')
            ->with('success', 'Conta bancária eliminada com sucesso.');
    }
}
