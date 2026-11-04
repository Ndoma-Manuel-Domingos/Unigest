<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\CashRegister;
use App\Models\CashTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CashTransferController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = CashTransfer::query()
            ->with([
                'sourceCashRegister.user',
                'destinationCashRegister.user',
                'initiator',
                'receiver',
            ])
            ->where('tenant_id', $tenantId);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transfer_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transfer_date', '<=', $request->date_to);
        }

        $transfers = $query
            ->latest('transfer_date')
            ->paginate(20)
            ->withQueryString();

        return view('university.finance.treasury.transfers.index', compact('transfers'));
    }

    public function create()
    {
        $tenantId = auth()->user()->tenant_id;

        $cashRegisters = CashRegister::query()
            ->with('user')
            ->where('tenant_id', $tenantId)
            ->where('status', 'open')
            ->orderBy('opened_at')
            ->get();

        return view('university.finance.treasury.transfers.create', compact('cashRegisters'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'source_cash_register_id' => ['required', 'integer'],
            'destination_cash_register_id' => ['required', 'integer', 'different:source_cash_register_id'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $tenantId = auth()->user()->tenant_id;

        $transfer = DB::transaction(function () use ($data, $tenantId) {
            $registers = CashRegister::query()
                ->where('tenant_id', $tenantId)
                ->whereIn('id', [
                    $data['source_cash_register_id'],
                    $data['destination_cash_register_id'],
                ])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($registers->count() !== 2) {
                throw ValidationException::withMessages([
                    'destination_cash_register_id' => 'Uma das caixas seleccionadas não pertence à universidade.',
                ]);
            }

            $source = $registers->get($data['source_cash_register_id']);
            $destination = $registers->get($data['destination_cash_register_id']);

            if ($source->status !== 'open' || $destination->status !== 'open') {
                throw ValidationException::withMessages([
                    'destination_cash_register_id' => 'As duas caixas têm de estar abertas.',
                ]);
            }

            if ((int) $source->user_id === (int) $destination->user_id) {
                throw ValidationException::withMessages([
                    'destination_cash_register_id' => 'A transferência deve ser entre operadores diferentes.',
                ]);
            }

            if ((float) $data['amount'] > (float) $source->expected_balance) {
                throw ValidationException::withMessages([
                    'amount' => 'O valor ultrapassa o saldo disponível na caixa de origem.',
                ]);
            }

            return CashTransfer::create([
                'tenant_id' => $tenantId,
                'source_cash_register_id' => $source->id,
                'destination_cash_register_id' => $destination->id,
                'initiated_by' => auth()->id(),
                'transfer_number' => $this->generateTransferNumber($tenantId),
                'amount' => $data['amount'],
                'status' => 'pending',
                'transfer_date' => now(),
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('university.treasury.transfers.show', $transfer)
            ->with('success', 'Transferência criada. Aguarda a confirmação do operador de destino.');
    }

    public function show(CashTransfer $cashTransfer)
    {
        $this->ensureTenant($cashTransfer);

        $cashTransfer->load([
            'sourceCashRegister.user',
            'destinationCashRegister.user',
            'initiator',
            'receiver',
        ]);

        return view('university.finance.treasury.transfers.show', compact('cashTransfer'));
    }

    public function receive(Request $request, CashTransfer $cashTransfer)
    {

        $this->ensureTenant($cashTransfer);

        $data = $request->validate([
            'receiver_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($cashTransfer, $data) {
            $transfer = CashTransfer::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->lockForUpdate()
                ->findOrFail($cashTransfer->id);

            if ($transfer->status !== 'pending') {
                throw ValidationException::withMessages([
                    'transfer' => 'Esta transferência já foi processada.',
                ]);
            }

            $registers = CashRegister::query()
                ->where('tenant_id', auth()->user()->tenant_id)
                ->whereIn('id', [
                    $transfer->source_cash_register_id,
                    $transfer->destination_cash_register_id,
                ])
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $source = $registers->get($transfer->source_cash_register_id);
            $destination = $registers->get($transfer->destination_cash_register_id);

            if ((int) $destination->user_id !== (int) auth()->id()) {
                abort(403, 'Só o operador da caixa de destino pode confirmar a recepção.');
            }

            if (!$source || !$destination ||
                $source->status !== 'open' ||
                $destination->status !== 'open') {
                throw ValidationException::withMessages([
                    'transfer' => 'As duas caixas têm de continuar abertas para concluir a transferência.',
                ]);
            }

            if ((float) $transfer->amount > (float) $source->expected_balance) {
                throw ValidationException::withMessages([
                    'transfer' => 'O saldo da caixa de origem já não é suficiente.',
                ]);
            }

            $amount = (float) $transfer->amount;

            CashMovement::create([
                'tenant_id' => $transfer->tenant_id,
                'cash_register_id' => $source->id,
                'user_id' => auth()->id(),
                'type' => 'expense',
                'source' => 'cash_transfer',
                'description' => 'Transferência enviada: ' . $transfer->transfer_number,
                'amount' => $amount,
                'movement_date' => now(),
                'observations' => $data['receiver_notes'] ?? null,
            ]);

            CashMovement::create([
                'tenant_id' => $transfer->tenant_id,
                'cash_register_id' => $destination->id,
                'user_id' => auth()->id(),
                'type' => 'income',
                'source' => 'cash_transfer',
                'description' => 'Transferência recebida: ' . $transfer->transfer_number,
                'amount' => $amount,
                'movement_date' => now(),
                'observations' => $data['receiver_notes'] ?? null,
            ]);

            $source->expected_balance = (float) $source->expected_balance - $amount;
            $source->save();

            $destination->expected_balance = (float) $destination->expected_balance + $amount;
            $destination->save();

            $transfer->update([
                'status' => 'received',
                'received_by' => auth()->id(),
                'received_at' => now(),
                'receiver_notes' => $data['receiver_notes'] ?? null,
            ]);
        });

        return back()->with('success', 'Recepção confirmada. Os saldos das duas caixas foram actualizados.');
    }

    public function reject(Request $request, CashTransfer $cashTransfer)
    {
        $this->ensureTenant($cashTransfer);

        $data = $request->validate([
            'receiver_notes' => ['required', 'string', 'max:2000'],
        ]);

        $destination = CashRegister::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->findOrFail($cashTransfer->destination_cash_register_id);

        if ((int) $destination->user_id !== (int) auth()->id()) {
            abort(403, 'Só o operador da caixa de destino pode rejeitar a transferência.');
        }

        $updated = CashTransfer::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->whereKey($cashTransfer->id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'received_by' => auth()->id(),
                'receiver_notes' => $data['receiver_notes'],
                'updated_at' => now(),
            ]);

        if (!$updated) {
            throw ValidationException::withMessages([
                'transfer' => 'Esta transferência já foi processada.',
            ]);
        }

        return back()->with('success', 'Transferência rejeitada. Nenhum saldo foi alterado.');
    }

    public function cancel(CashTransfer $cashTransfer)
    {
        $this->ensureTenant($cashTransfer);

        $updated = CashTransfer::query()
            ->where('tenant_id', auth()->user()->tenant_id)
            ->whereKey($cashTransfer->id)
            ->where('initiated_by', auth()->id())
            ->where('status', 'pending')
            ->update([
                'status' => 'cancelled',
                'updated_at' => now(),
            ]);

        if (!$updated) {
            throw ValidationException::withMessages([
                'transfer' => 'Só pode cancelar uma transferência pendente que tenha iniciado.',
            ]);
        }

        return back()->with('success', 'Transferência cancelada.');
    }

    private function ensureTenant(CashTransfer $transfer): void
    {
        abort_unless(
            (int) $transfer->tenant_id === (int) auth()->user()->tenant_id,
            404
        );
    }

    private function generateTransferNumber(int $tenantId): string
    {
        do {
            $number = 'TRC-' . now()->format('YmdHis') . '-' . random_int(100, 999);
        } while (
            CashTransfer::query()
                ->where('tenant_id', $tenantId)
                ->where('transfer_number', $number)
                ->exists()
        );

        return $number;
    }
}
