<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\CashCount;
use App\Models\CashRegister;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashCountController extends Controller
{
    private array $denominations = [
        5000, 2000, 1000, 500, 200, 100, 50, 10, 5, 1,
    ];

    public function index(Request $request)
    {
        $query = CashCount::query()
            ->with(['cashRegister.user', 'counter', 'reviewer'])
            ->where('tenant_id', auth()->user()->tenant_id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('counted_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('counted_at', '<=', $request->date_to);
        }

        $counts = $query->latest('counted_at')
            ->paginate(20)
            ->withQueryString();

        return view('university.finance.treasury.cash-counts.index', compact('counts'));
    }

    public function create()
    {
        $cashRegisters = CashRegister::query()
            ->with('user')
            ->where('tenant_id', auth()->user()->tenant_id)
            ->where('status', 'open')
            ->where('user_id', auth()->id())
            ->orderByDesc('opened_at')
            ->get();

        return view(
            'university.finance.treasury.cash-counts.create',
            [
                'cashRegisters' => $cashRegisters,
                'denominations' => $this->denominations,
            ]
        );
    }

    public function store(Request $request)
    {
        $rules = [
            'cash_register_id' => ['required', 'integer'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];

        foreach ($this->denominations as $value) {
            $rules["quantities.$value"] = [
                'nullable',
                'integer',
                'min:0',
                'max:100000',
            ];
        }

        $data = $request->validate($rules);

        $tenantId = auth()->user()->tenant_id;

        $cashCount = DB::transaction(function () use ($data, $tenantId) {
            $register = CashRegister::query()
                ->where('tenant_id', $tenantId)
                ->lockForUpdate()
                ->findOrFail($data['cash_register_id']);

            if (
                $register->status !== 'open' ||
                (int) $register->user_id !== (int) auth()->id()
            ) {
                throw ValidationException::withMessages([
                    'cash_register_id' => 'Só pode contar uma caixa aberta que esteja atribuída a si.',
                ]);
            }

            $denominations = [];
            $countedBalance = 0;

            foreach ($this->denominations as $value) {
                $quantity = (int) ($data['quantities'][$value] ?? 0);
                $subtotal = $value * $quantity;

                $denominations[] = [
                    'value' => $value,
                    'quantity' => $quantity,
                    'subtotal' => $subtotal,
                ];

                $countedBalance += $subtotal;
            }

            $expectedBalance = (float) $register->expected_balance;

            return CashCount::create([
                'tenant_id' => $tenantId,
                'cash_register_id' => $register->id,
                'counted_by' => auth()->id(),
                'reference' => $this->generateReference($tenantId),
                'denominations' => $denominations,
                'expected_balance' => $expectedBalance,
                'counted_balance' => $countedBalance,
                'difference' => round($countedBalance - $expectedBalance, 2),
                'status' => 'pending',
                'counted_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('university.treasury.cash-counts.show', $cashCount)
            ->with('success', 'Contagem registada e enviada para aprovação.');
    }

    public function show(CashCount $cashCount)
    {
        $this->ensureTenant($cashCount);

        $cashCount->load([
            'cashRegister.user',
            'counter',
            'reviewer',
        ]);

        return view(
            'university.finance.treasury.cash-counts.show',
            compact('cashCount')
        );
    }

    public function approve(Request $request, CashCount $cashCount)
    {
        $this->ensureTenant($cashCount);

        $data = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($cashCount, $data) {
            $count = CashCount::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($cashCount->id);

            if ((int) $count->counted_by === (int) auth()->id()) {
                throw ValidationException::withMessages([
                    'cash_count' => 'Não pode aprovar uma contagem que registou.',
                ]);
            }

            if ($count->status !== 'pending') {
                throw ValidationException::withMessages([
                    'cash_count' => 'Esta contagem já foi analisada.',
                ]);
            }

            $count->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $data['review_notes'] ?? null,
            ]);

            // Guarda a última contagem física aprovada na caixa.
            $register = CashRegister::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($count->cash_register_id);

            $register->update([
                'counted_balance' => $count->counted_balance,
                'difference' => $count->difference,
            ]);
        });

        return back()->with('success', 'Contagem aprovada com sucesso.');
    }

    public function reject(Request $request, CashCount $cashCount)
    {
        $this->ensureTenant($cashCount);

        $data = $request->validate([
            'review_notes' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($cashCount, $data) {
            $count = CashCount::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($cashCount->id);

            if ((int) $count->counted_by === (int) auth()->id()) {
                throw ValidationException::withMessages([
                    'cash_count' => 'Não pode rejeitar a própria contagem.',
                ]);
            }

            if ($count->status !== 'pending') {
                throw ValidationException::withMessages([
                    'cash_count' => 'Esta contagem já foi analisada.',
                ]);
            }

            $count->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $data['review_notes'],
            ]);
        });

        return back()->with('success', 'Contagem rejeitada. O saldo da caixa não foi alterado.');
    }

    private function ensureTenant(CashCount $cashCount): void
    {
        abort_unless(
            (int) $cashCount->tenant_id === (int) auth()->user()->tenant_id,
            404
        );
    }

    private function generateReference(int $tenantId): string
    {
        do {
            $reference = 'CNT-' . now()->format('YmdHis') . '-' . random_int(100, 999);
        } while (
            CashCount::query()
                ->where('tenant_id', $tenantId)
                ->where('reference', $reference)
                ->exists()
        );

        return $reference;
    }
}
