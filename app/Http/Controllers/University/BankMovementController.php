<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Services\Finance\FinancialAuditService;

use Illuminate\Support\Str;

class BankMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = BankMovement::query()
            ->with(['bankAccount', 'user'])
            ->latest('movement_date')
            ->latest('id');

        if ($request->filled('bank_account_id')) {
            $query->where('bank_account_id', $request->integer('bank_account_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('movement_date', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('movement_date', '<=', $request->date('date_to'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');

            $query->where(function ($q) use ($search) {
                $q->where('description', 'ILIKE', "%{$search}%")
                    ->orWhere('reference', 'ILIKE', "%{$search}%")
                    ->orWhere('document_number', 'ILIKE', "%{$search}%")
                    ->orWhere('transaction_reference', 'ILIKE', "%{$search}%");
            });
        }

        $movements = $query
            ->paginate(20)
            ->withQueryString();

        $accounts = BankAccount::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'university.finance.bank-movements.index',
            compact('movements', 'accounts')
        );
    }

    public function create()
    {
        $accounts = BankAccount::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'university.finance.bank-movements.create',
            compact('accounts')
        );
    }

    public function store(Request $request, FinancialAuditService $audit)
    {
        $data = $request->validate([
            'bank_account_id' => ['required', 'integer'],
            'type' => ['required', Rule::in([
                'income',
                'expense',
                'adjustment',
            ])],
            'adjustment_direction' => [
                Rule::requiredIf($request->input('type') === 'adjustment'),
                'nullable',
                Rule::in(['positive', 'negative']),
            ],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'movement_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'document_number' => ['nullable', 'string', 'max:100'],
            'transaction_reference' => ['nullable', 'string', 'max:150'],
            'observations' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data, $audit) {
            // A pesquisa respeita o escopo do tenant.
            $account = BankAccount::query()
                ->lockForUpdate()
                ->findOrFail($data['bank_account_id']);

            if (!$account->is_active) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'bank_account_id' => 'A conta seleccionada está inactiva.',
                ]);
            }

            $balance = (float) $account->current_balance;
            $amount = (float) $data['amount'];

            $direction = match ($data['type']) {
                'income' => 'positive',
                'expense' => 'negative',
                'adjustment' => $data['adjustment_direction'],
            };

            $newBalance = $direction === 'positive' ? $balance + $amount : $balance - $amount;

            if ($newBalance < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'A operação não pode deixar o saldo bancário negativo.',
                ]);
            }

            $movement = BankMovement::create([
                ...$data,
                'adjustment_direction' => $data['type'] === 'adjustment' ? $data['adjustment_direction'] : null,
                'user_id' => auth()->id(),
                'reconciled' => false,
            ]);

            $account->update([
                'current_balance' => $newBalance,
            ]);

            $audit->record(
                module: 'banking',
                event: 'bank_movement.created',
                description: 'Foi criado um movimento bancário.',
                record: $movement,
                newValues: [
                    'type' => $movement->type,
                    'amount' => $movement->amount,
                    'reference' => $movement->reference,
                    'bank_account_id' => $movement->bank_account_id,
                    'movement_date' => $movement->movement_date,
                ]
            );
        });

        return redirect()
            ->route('university.bank-movements.index')
            ->with('success', 'Movimento bancário registado com sucesso.');
    }

    public function show(string $bankMovement)
    {
        $bankMovement = BankMovement::findOrFail($bankMovement);

        $bankMovement->load([
            'bankAccount',
            'user',
        ]);

        return view(
            'university.finance.bank-movements.show',
            compact('bankMovement')
        );
    }


    public function transferCreate()
    {
        $accounts = BankAccount::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($accounts->count() < 2) {
            return redirect()
                ->route('university.bank-accounts.index')
                ->with('error', 'Precisa de pelo menos duas contas bancárias activas para efectuar uma transferência.');
        }

        return view(
            'university.finance.bank-movements.transfer',
            compact('accounts')
        );
    }

    public function transferStore(Request $request)
    {
        $data = $request->validate([
            'source_account_id' => ['required', 'integer', 'different:destination_account_id'],
            'destination_account_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'movement_date' => ['required', 'date'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_reference' => ['nullable', 'string', 'max:150'],
            'observations' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data) {
            // Bloqueia as contas numa ordem consistente para reduzir conflitos.
            $ids = [
                (int) $data['source_account_id'],
                (int) $data['destination_account_id'],
            ];

            sort($ids);

            $accounts = BankAccount::query()
                ->whereIn('id', $ids)
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($accounts->count() !== 2) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'destination_account_id' => 'Uma das contas seleccionadas não existe ou não pertence à universidade actual.',
                ]);
            }

            $source = $accounts->get((int) $data['source_account_id']);
            $destination = $accounts->get((int) $data['destination_account_id']);

            if (!$source->is_active || !$destination->is_active) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'source_account_id' => 'As duas contas têm de estar activas.',
                ]);
            }

            if ($source->currency !== $destination->currency) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'destination_account_id' => 'As contas devem utilizar a mesma moeda. Não é possível transferir entre moedas diferentes sem uma operação cambial.',
                ]);
            }

            $amount = (float) $data['amount'];

            if ((float) $source->current_balance < $amount) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'A conta de origem não tem saldo suficiente.',
                ]);
            }

            $groupId = (string) Str::uuid();
            $reference = 'TRANS-' . strtoupper(Str::random(10));

            $common = [
                'tenant_id' => $source->tenant_id,
                'user_id' => auth()->id(),
                'amount' => $amount,
                'movement_date' => $data['movement_date'],
                'reference' => $reference,
                'transaction_reference' => $data['transaction_reference'] ?? null,
                'transfer_group_id' => $groupId,
                'reconciled' => false,
                'observations' => $data['observations'] ?? null,
            ];

            BankMovement::create([
                ...$common,
                'bank_account_id' => $source->id,
                'type' => 'transfer_out',
                'description' => 'Transferência para ' . $destination->name . ': ' . $data['description'],
            ]);

            BankMovement::create([
                ...$common,
                'bank_account_id' => $destination->id,
                'type' => 'transfer_in',
                'description' => 'Transferência recebida de ' . $source->name . ': ' . $data['description'],
            ]);

            $source->update([
                'current_balance' => (float) $source->current_balance - $amount,
            ]);

            $destination->update([
                'current_balance' => (float) $destination->current_balance + $amount,
            ]);
        });

        return redirect()
            ->route('university.bank-movements.index')
            ->with('success', 'Transferência bancária efectuada com sucesso.');
    }


}
