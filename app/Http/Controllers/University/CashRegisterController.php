<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CashRegisterController extends Controller
{
    public function index(Request $request)
    {
        $registers = CashRegister::with('user')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('operator'), function ($query) use ($request) {
                $query->whereHas('user', function ($userQuery) use ($request) {
                    $userQuery->where('name', 'ILIKE', '%' . $request->operator . '%');
                });
            })
            ->latest('opened_at')
            ->paginate(15)
            ->withQueryString();

        $openRegister = CashRegister::where('user_id', auth()->id())
            ->where('status', 'open')
            ->first();

        return view('university.finance.cash.index', compact(
            'registers',
            'openRegister'
        ));
    }

    public function create()
    {
        $existing = CashRegister::where('user_id', auth()->id())
            ->where('status', 'open')
            ->exists();

        if ($existing) {
            return redirect()
                ->route('university.cash.index')
                ->with('error', 'Já possui um caixa aberto.');
        }

        return view('university.finance.cash.open');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'opening_balance' => ['required', 'numeric', 'min:0'],
            'opening_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $tenantId = auth()->user()->tenant_id;

        DB::transaction(function () use ($data, $tenantId) {
            // Serializa a abertura de caixas dentro da universidade.
            University::whereKey($tenantId)->lockForUpdate()->firstOrFail();

            $alreadyOpen = CashRegister::where('user_id', auth()->id())
                ->where('status', 'open')
                ->exists();

            if ($alreadyOpen) {
                throw new \RuntimeException('Já existe um caixa aberto para este operador.');
            }

            CashRegister::create([
                'user_id' => auth()->id(),
                'opened_at' => now(),
                'opening_balance' => $data['opening_balance'],
                'expected_balance' => $data['opening_balance'],
                'status' => 'open',
                'opening_notes' => $data['opening_notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('university.cash.index')
            ->with('success', 'Caixa aberto com sucesso.');
    }

    public function show(string $cashRegister)
    {

        $cashRegister = CashRegister::findOrFail($cashRegister); 

        $cashRegister->load([
            'user',
            'movements.user',
            'movements.payment.invoice.student',
        ]);

        $income = (float) $cashRegister->movements()
            ->where('type', 'income')
            ->sum('amount');

        $expense = (float) $cashRegister->movements()
            ->where('type', 'expense')
            ->sum('amount');

        $expectedBalance = (float) $cashRegister->opening_balance
            + $income
            - $expense;

        return view('university.finance.cash.show', compact(
            'cashRegister',
            'income',
            'expense',
            'expectedBalance'
        ));
    }

    public function movementForm(string $cashRegister)
    {
    
        $cashRegister = CashRegister::findOrFail($cashRegister); 

        $this->ensureOpenAndOwned($cashRegister->id);

        return view('university.finance.cash.movement', compact('cashRegister'));
    }

    public function storeMovement(Request $request, string $cashRegister)
    {
        $cashRegister = CashRegister::findOrFail($cashRegister); 

        $this->ensureOpenAndOwned($cashRegister->id);

        $data = $request->validate([
            'type' => ['required', Rule::in(['income', 'expense'])],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'movement_date' => ['required', 'date'],
            'observations' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($cashRegister, $data) {
            $register = CashRegister::whereKey($cashRegister->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($register->status !== 'open') {
                throw new \RuntimeException('Este caixa já está fechado.');
            }

            CashMovement::create([
                'cash_register_id' => $register->id,
                'user_id' => auth()->id(),
                'type' => $data['type'],
                'source' => 'manual',
                'description' => $data['description'],
                'amount' => $data['amount'],
                'movement_date' => $data['movement_date'],
                'observations' => $data['observations'] ?? null,
            ]);
        });

        return redirect()
            ->route('university.cash.show', $cashRegister)
            ->with('success', 'Movimento registado com sucesso.');
    }

    public function closeForm(string $cashRegister)
    {

        $cashRegister = CashRegister::findOrFail($cashRegister); 

        $this->ensureOpenAndOwned($cashRegister->id);

        $income = (float) $cashRegister->movements()
            ->where('type', 'income')
            ->sum('amount');

        $expense = (float) $cashRegister->movements()
            ->where('type', 'expense')
            ->sum('amount');

        $expectedBalance = (float) $cashRegister->opening_balance
            + $income
            - $expense;

        return view('university.finance.cash.close', compact(
            'cashRegister',
            'income',
            'expense',
            'expectedBalance'
        ));
    }

    public function close(Request $request, string $cashRegister)
    {
        $cashRegister = CashRegister::findOrFail($cashRegister); 

        $this->ensureOpenAndOwned($cashRegister->id);

        $data = $request->validate([
            'counted_balance' => ['required', 'numeric', 'min:0'],
            'closing_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($cashRegister, $data) {
            $register = CashRegister::whereKey($cashRegister->id)
                ->lockForUpdate()
            ->firstOrFail();

            if ($register->status !== 'open') {
                throw ValidationException::withMessages([
                    'cash' => 'Este caixa já foi fechado.',
                ]);
            }

            $income = (float) $register->movements()
                ->where('type', 'income')
                ->sum('amount');

            $expense = (float) $register->movements()
                ->where('type', 'expense')
                ->sum('amount');

            $expected = (float) $register->opening_balance
                + $income
                - $expense;

            $counted = (float) $data['counted_balance'];

            $register->update([
                'expected_balance' => $expected,
                'counted_balance' => $counted,
                'difference' => $counted - $expected,
                'closed_at' => now(),
                'closing_notes' => $data['closing_notes'] ?? null,
                'status' => 'closed',
            ]);
        });

        return redirect()
            ->route('university.cash.show', $cashRegister)
            ->with('success', 'Caixa fechado com sucesso.');
    }

    private function ensureOpenAndOwned(string $cashRegister): void
    {
        $cashRegister = CashRegister::findOrFail($cashRegister); 

        abort_unless(
            $cashRegister->user_id === auth()->id(),
            403,
            'Não tem autorização para operar este caixa.'
        );

        abort_unless(
            $cashRegister->status === 'open',
            403,
            'Este caixa não está aberto.'
        );
    }
}
