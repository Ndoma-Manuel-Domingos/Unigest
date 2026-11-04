<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\PettyCashFund;
use App\Models\User;
use App\Models\PettyCashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PettyCashFundController extends Controller
{
    public function index()
    {
        $funds = PettyCashFund::query()
            ->with(['responsible', 'department'])
            ->withCount('movements')
            ->latest()
            ->paginate(20);

        return view('university.finance.petty-cash.funds.index', compact('funds'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;

        $users = User::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name']);

        $departments = Department::where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('university.finance.petty-cash.funds.create', compact(
            'users',
            'departments'
        ));
    }

    public function store(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $data = $request->validate([
            'responsible_user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('tenant_id', $tenantId),
            ],
            'department_id' => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id')->where('tenant_id', $tenantId),
            ],
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('petty_cash_funds', 'code')->where('tenant_id', $tenantId),
            ],
            'currency' => ['required', 'string', 'size:3'],
            'authorized_amount' => ['required', 'numeric', 'gt:0'],
            'initial_balance' => ['required', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        if ((float) $data['initial_balance'] > (float) $data['authorized_amount']) {
            return back()->withInput()->withErrors([
                'initial_balance' => 'O saldo inicial não pode ultrapassar o valor autorizado.',
            ]);
        }

        $fund = DB::transaction(function () use ($data, $tenantId) {
            $fund = PettyCashFund::create([
                'tenant_id' => $tenantId,
                'responsible_user_id' => $data['responsible_user_id'],
                'department_id' => $data['department_id'] ?? null,
                'name' => $data['name'],
                'code' => strtoupper($data['code']),
                'currency' => strtoupper($data['currency']),
                'authorized_amount' => $data['authorized_amount'],
                'current_balance' => $data['initial_balance'],
                'is_active' => true,
                'description' => $data['description'] ?? null,
            ]);

            if ((float) $data['initial_balance'] > 0) {
                PettyCashMovement::create([
                    'tenant_id' => $tenantId,
                    'petty_cash_fund_id' => $fund->id,
                    'user_id' => auth()->id(),
                    'approved_by' => auth()->id(),
                    'movement_number' => 'ABR-' . now()->format('YmdHis') . '-' . random_int(100, 999),
                    'type' => 'adjustment',
                    'status' => 'approved',
                    'amount' => $data['initial_balance'],
                    'description' => 'Abertura do fundo fixo',
                    'movement_date' => now()->toDateString(),
                    'approved_at' => now(),
                ]);
            }

            return $fund;
        });

        return redirect()
            ->route('university.petty-cash.funds.show', $fund)
            ->with('success', 'Fundo fixo criado com sucesso.');
    }

    public function show(PettyCashFund $pettyCashFund)
    {
        $this->ensureTenant($pettyCashFund);

        $pettyCashFund->load(['responsible', 'department']);

        $movements = $pettyCashFund->movements()
            ->with(['user', 'approver'])
            ->paginate(20);

        $pendingReplenishments = $pettyCashFund->replenishments()
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        return view('university.finance.petty-cash.funds.show', compact(
            'pettyCashFund',
            'movements',
            'pendingReplenishments'
        ));
    }

    public function edit(PettyCashFund $pettyCashFund)
    {
        $this->ensureTenant($pettyCashFund);

        $tenantId = auth()->user()->tenant_id;

        $users = User::where('tenant_id', $tenantId)->orderBy('name')->get(['id', 'name']);
        $departments = Department::where('tenant_id', $tenantId)->orderBy('name')->get(['id', 'name']);

        return view('university.finance.petty-cash.funds.edit', compact(
            'pettyCashFund',
            'users',
            'departments'
        ));
    }

    public function update(Request $request, PettyCashFund $pettyCashFund)
    {
        $this->ensureTenant($pettyCashFund);

        $tenantId = auth()->user()->tenant_id;

        $data = $request->validate([
            'responsible_user_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('tenant_id', $tenantId),
            ],
            'department_id' => [
                'nullable',
                'integer',
                Rule::exists('departments', 'id')->where('tenant_id', $tenantId),
            ],
            'name' => ['required', 'string', 'max:150'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('petty_cash_funds', 'code')
                    ->where('tenant_id', $tenantId)
                    ->ignore($pettyCashFund->id),
            ],
            'currency' => ['required', 'string', 'size:3'],
            'authorized_amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        if ((float) $data['authorized_amount'] < (float) $pettyCashFund->current_balance) {
            return back()->withInput()->withErrors([
                'authorized_amount' => 'O limite autorizado não pode ser inferior ao saldo actual do fundo.',
            ]);
        }

        $pettyCashFund->update([
            ...$data,
            'code' => strtoupper($data['code']),
            'currency' => strtoupper($data['currency']),
        ]);

        return redirect()
            ->route('university.petty-cash.funds.show', $pettyCashFund)
            ->with('success', 'Fundo actualizado com sucesso.');
    }

    public function toggleStatus(PettyCashFund $pettyCashFund)
    {
        $this->ensureTenant($pettyCashFund);

        if ($pettyCashFund->is_active && (float) $pettyCashFund->current_balance > 0) {
            return back()->withErrors([
                'fund' => 'Não pode desactivar um fundo que ainda possui saldo. Registe primeiro a devolução ou transferência do saldo.',
            ]);
        }

        $pettyCashFund->update([
            'is_active' => !$pettyCashFund->is_active,
        ]);

        return back()->with('success', 'Estado do fundo actualizado.');
    }

    private function ensureTenant(PettyCashFund $fund): void
    {
        abort_unless(
            (int) $fund->tenant_id === (int) auth()->user()->tenant_id,
            404
        );
    }
}
