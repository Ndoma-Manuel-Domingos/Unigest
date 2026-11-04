<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\PettyCashFund;
use App\Models\PettyCashMovement;
use App\Models\PettyCashReplenishment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PettyCashReplenishmentController extends Controller
{
    public function index(Request $request)
    {
        $query = PettyCashReplenishment::query()
            ->with(['fund', 'requester', 'reviewer'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('fund_id')) {
            $query->where('petty_cash_fund_id', $request->fund_id);
        }

        $replenishments = $query->paginate(20)->withQueryString();

        $funds = PettyCashFund::orderBy('name')->get();

        return view(
            'university.finance.petty-cash.replenishments.index',
            compact('replenishments', 'funds')
        );
    }

    public function create(PettyCashFund $pettyCashFund)
    {
        $this->ensureTenant($pettyCashFund);

        abort_unless($pettyCashFund->is_active, 422, 'Este fundo está inactivo.');

        abort_unless(
            (int) $pettyCashFund->responsible_user_id === (int) auth()->id(),
            403,
            'Só o responsável pelo fundo pode solicitar reposição.'
        );

        return view(
            'university.finance.petty-cash.replenishments.create',
            compact('pettyCashFund')
        );
    }

    public function store(Request $request, PettyCashFund $pettyCashFund)
    {
        $this->ensureTenant($pettyCashFund);

        abort_unless(
            (int) $pettyCashFund->responsible_user_id === (int) auth()->id(),
            403
        );

        $data = $request->validate([
            'requested_amount' => ['required', 'numeric', 'gt:0'],
            'request_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        $replenishment = DB::transaction(function () use ($data, $pettyCashFund) {
            $fund = PettyCashFund::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($pettyCashFund->id);

            if (!$fund->is_active) {
                throw ValidationException::withMessages([
                    'fund' => 'Este fundo está inactivo.',
                ]);
            }

            $pendingExists = PettyCashReplenishment::query()
                ->where('tenant_id', $fund->tenant_id)
                ->where('petty_cash_fund_id', $fund->id)
                ->whereIn('status', ['pending', 'approved'])
                ->exists();

            if ($pendingExists) {
                throw ValidationException::withMessages([
                    'requested_amount' => 'Já existe um pedido de reposição pendente ou aprovado para este fundo.',
                ]);
            }

            $remainingCapacity = (float) $fund->authorized_amount
                - (float) $fund->current_balance;

            if ((float) $data['requested_amount'] > $remainingCapacity) {
                throw ValidationException::withMessages([
                    'requested_amount' => 'O valor solicitado ultrapassa a capacidade disponível até ao limite autorizado do fundo.',
                ]);
            }

            return PettyCashReplenishment::create([
                'tenant_id' => $fund->tenant_id,
                'petty_cash_fund_id' => $fund->id,
                'requested_by' => auth()->id(),
                'request_number' => $this->generateRequestNumber($fund->tenant_id),
                'requested_amount' => $data['requested_amount'],
                'status' => 'pending',
                'request_date' => $data['request_date'],
                'reason' => $data['reason'],
            ]);
        });

        return redirect()
            ->route('university.petty-cash.replenishments.show', $replenishment)
            ->with('success', 'Pedido de reposição submetido com sucesso.');
    }

    public function show(PettyCashReplenishment $replenishment)
    {
        $this->ensureTenant($replenishment);

        $replenishment->load([
            'fund',
            'requester',
            'reviewer',
            'movement',
        ]);

        return view(
            'university.finance.petty-cash.replenishments.show',
            compact('replenishment')
        );
    }

    public function approve(Request $request, PettyCashReplenishment $replenishment)
    {
        $this->ensureTenant($replenishment);

        $data = $request->validate([
            'approved_amount' => ['required', 'numeric', 'gt:0'],
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($replenishment, $data) {
            $record = PettyCashReplenishment::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($replenishment->id);

            if ($record->status !== 'pending') {
                throw ValidationException::withMessages([
                    'replenishment' => 'Este pedido já foi processado.',
                ]);
            }

            if ((int) $record->requested_by === (int) auth()->id()) {
                throw ValidationException::withMessages([
                    'replenishment' => 'Não pode aprovar o seu próprio pedido.',
                ]);
            }

            if ((float) $data['approved_amount'] > (float) $record->requested_amount) {
                throw ValidationException::withMessages([
                    'approved_amount' => 'O valor aprovado não pode ultrapassar o valor solicitado.',
                ]);
            }

            $fund = PettyCashFund::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($record->petty_cash_fund_id);

            $remainingCapacity = (float) $fund->authorized_amount
                - (float) $fund->current_balance;

            if ((float) $data['approved_amount'] > $remainingCapacity) {
                throw ValidationException::withMessages([
                    'approved_amount' => 'O valor aprovado ultrapassa a capacidade disponível do fundo.',
                ]);
            }

            $record->update([
                'approved_amount' => $data['approved_amount'],
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $data['review_notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Pedido de reposição aprovado.');
    }

    public function reject(Request $request, PettyCashReplenishment $replenishment)
    {
        $this->ensureTenant($replenishment);

        $data = $request->validate([
            'review_notes' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($replenishment, $data) {
            $record = PettyCashReplenishment::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($replenishment->id);

            if ($record->status !== 'pending') {
                throw ValidationException::withMessages([
                    'replenishment' => 'Só é possível rejeitar pedidos pendentes.',
                ]);
            }

            if ((int) $record->requested_by === (int) auth()->id()) {
                throw ValidationException::withMessages([
                    'replenishment' => 'Não pode rejeitar o seu próprio pedido.',
                ]);
            }

            $record->update([
                'status' => 'rejected',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $data['review_notes'],
            ]);
        });

        return back()->with('success', 'Pedido de reposição rejeitado.');
    }

    public function pay(PettyCashReplenishment $replenishment)
    {
        $this->ensureTenant($replenishment);

        DB::transaction(function () use ($replenishment) {
            $record = PettyCashReplenishment::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($replenishment->id);

            if ($record->status !== 'approved') {
                throw ValidationException::withMessages([
                    'replenishment' => 'Só é possível pagar pedidos aprovados e ainda não pagos.',
                ]);
            }

            $fund = PettyCashFund::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($record->petty_cash_fund_id);

            if (!$fund->is_active) {
                throw ValidationException::withMessages([
                    'fund' => 'Não é possível repor um fundo inactivo.',
                ]);
            }

            $amount = (float) $record->approved_amount;

            $remainingCapacity = (float) $fund->authorized_amount
                - (float) $fund->current_balance;

            if ($amount <= 0 || $amount > $remainingCapacity) {
                throw ValidationException::withMessages([
                    'replenishment' => 'O saldo ou limite autorizado do fundo foi alterado. Verifique o pedido antes de pagar.',
                ]);
            }

            $movementNumber = $this->generateMovementNumber($fund->tenant_id);

            $movement = PettyCashMovement::create([
                'tenant_id' => $fund->tenant_id,
                'petty_cash_fund_id' => $fund->id,
                'user_id' => auth()->id(),
                'approved_by' => $record->reviewed_by,
                'movement_number' => $movementNumber,
                'type' => 'replenishment',
                'status' => 'approved',
                'amount' => $amount,
                'description' => 'Reposição do fundo - ' . $record->request_number,
                'document_number' => $record->request_number,
                'movement_date' => now()->toDateString(),
                'approved_at' => now(),
                'notes' => 'Reposição paga pela tesouraria.',
            ]);

            $fund->current_balance = (float) $fund->current_balance + $amount;
            $fund->save();

            $record->update([
                'status' => 'paid',
                'movement_id' => $movement->id,
                'paid_at' => now(),
            ]);
        });

        return back()->with('success', 'Reposição paga e saldo do fundo actualizado.');
    }

    private function ensureTenant($record): void
    {
        abort_unless(
            (int) $record->tenant_id === (int) auth()->user()->tenant_id,
            404
        );
    }

    private function generateRequestNumber(int $tenantId): string
    {
        do {
            $number = 'REP-' . now()->format('YmdHis') . '-' . random_int(100, 999);
        } while (
            PettyCashReplenishment::query()
                ->where('tenant_id', $tenantId)
                ->where('request_number', $number)
                ->exists()
        );

        return $number;
    }

    private function generateMovementNumber(int $tenantId): string
    {
        do {
            $number = 'REP-MOV-' . now()->format('YmdHis') . '-' . random_int(100, 999);
        } while (
            PettyCashMovement::query()
                ->where('tenant_id', $tenantId)
                ->where('movement_number', $number)
                ->exists()
        );

        return $number;
    }
}
