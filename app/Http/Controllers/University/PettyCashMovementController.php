<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\PettyCashFund;
use App\Models\PettyCashMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class PettyCashMovementController extends Controller
{
    public function create(PettyCashFund $pettyCashFund)
    {
        $this->ensureTenant($pettyCashFund);

        abort_unless($pettyCashFund->is_active, 422, 'Fundo inactivo.');

        return view('university.finance.petty-cash.movements.create', compact('pettyCashFund'));
    }

    public function store(Request $request, PettyCashFund $pettyCashFund)
    {
        $this->ensureTenant($pettyCashFund);

        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['required', 'string', 'max:255'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'supplier_name' => ['nullable', 'string', 'max:150'],
            'movement_date' => ['required', 'date'],
            'receipt' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ((int) $pettyCashFund->responsible_user_id !== (int) auth()->id()) {
            abort(403, 'Só o responsável pelo fundo pode registar despesas.');
        }

        $path = $request->file('receipt')?->store(
            'petty-cash/receipts/' . $pettyCashFund->id,
            'private'
        );

        $movement = DB::transaction(function () use ($data, $pettyCashFund, $path) {
            $fund = PettyCashFund::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($pettyCashFund->id);

            if (!$fund->is_active) {
                throw ValidationException::withMessages([
                    'fund' => 'Este fundo está inactivo.',
                ]);
            }

            if ((float) $data['amount'] > (float) $fund->current_balance) {
                throw ValidationException::withMessages([
                    'amount' => 'O valor da despesa ultrapassa o saldo disponível.',
                ]);
            }

            return PettyCashMovement::create([
                'tenant_id' => $fund->tenant_id,
                'petty_cash_fund_id' => $fund->id,
                'user_id' => auth()->id(),
                'movement_number' => $this->generateNumber($fund->tenant_id, 'DES'),
                'type' => 'expense',
                'status' => 'pending',
                'amount' => $data['amount'],
                'description' => $data['description'],
                'document_number' => $data['document_number'] ?? null,
                'supplier_name' => $data['supplier_name'] ?? null,
                'movement_date' => $data['movement_date'],
                'receipt_path' => $path,
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('university.petty-cash.funds.show', $pettyCashFund)
            ->with('success', 'Despesa submetida para aprovação.');
    }

    public function approve(Request $request, PettyCashMovement $movement)
    {
        $this->ensureTenant($movement);

        $data = $request->validate([
            'approval_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($movement, $data) {
            $record = PettyCashMovement::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($movement->id);

            if ($record->status !== 'pending' || $record->type !== 'expense') {
                throw ValidationException::withMessages([
                    'movement' => 'Esta despesa já foi processada ou não pode ser aprovada por esta operação.',
                ]);
            }

            if ((int) $record->user_id === (int) auth()->id()) {
                throw ValidationException::withMessages([
                    'movement' => 'Não pode aprovar a sua própria despesa.',
                ]);
            }

            $fund = PettyCashFund::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($record->petty_cash_fund_id);

            if ((float) $record->amount > (float) $fund->current_balance) {
                throw ValidationException::withMessages([
                    'movement' => 'O saldo disponível já não cobre esta despesa.',
                ]);
            }

            $fund->current_balance = (float) $fund->current_balance - (float) $record->amount;
            $fund->save();

            $record->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $data['approval_notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Despesa aprovada e saldo actualizado.');
    }

    public function reject(Request $request, PettyCashMovement $movement)
    {
        $this->ensureTenant($movement);

        $data = $request->validate([
            'approval_notes' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($movement, $data) {
            $record = PettyCashMovement::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($movement->id);

            if ($record->status !== 'pending' || $record->type !== 'expense') {
                throw ValidationException::withMessages([
                    'movement' => 'Esta despesa já foi processada.',
                ]);
            }

            if ((int) $record->user_id === (int) auth()->id()) {
                throw ValidationException::withMessages([
                    'movement' => 'Não pode rejeitar a sua própria despesa.',
                ]);
            }

            $record->update([
                'status' => 'rejected',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_notes' => $data['approval_notes'],
            ]);
        });

        return back()->with('success', 'Despesa rejeitada. O saldo não foi alterado.');
    }

    private function ensureTenant(PettyCashFund|PettyCashMovement $record): void
    {
        abort_unless(
            (int) $record->tenant_id === (int) auth()->user()->tenant_id,
            404
        );
    }

    private function generateNumber(int $tenantId, string $prefix): string
    {
        do {
            $number = $prefix . '-' . now()->format('YmdHis') . '-' . random_int(100, 999);
        } while (
            PettyCashMovement::query()
                ->where('tenant_id', $tenantId)
                ->where('movement_number', $number)
                ->exists()
        );

        return $number;
    }
}
