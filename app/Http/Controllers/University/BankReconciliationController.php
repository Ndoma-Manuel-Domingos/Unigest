<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\BankMovement;
use App\Models\BankReconciliation;
use App\Models\BankStatementLine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BankReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $reconciliations = BankReconciliation::query()
            ->with(['bankAccount', 'creator'])
            ->when(
                $request->filled('bank_account_id'),
                fn ($query) => $query->where(
                    'bank_account_id',
                    $request->integer('bank_account_id')
                )
            )
            ->when(
                $request->filled('status'),
                fn ($query) => $query->where(
                    'status',
                    $request->string('status')->toString()
                )
            )
            ->when(
                $request->filled('date_from'),
                fn ($query) => $query->whereDate(
                    'period_start',
                    '>=',
                    $request->date_from
                )
            )
            ->when(
                $request->filled('date_to'),
                fn ($query) => $query->whereDate(
                    'period_end',
                    '<=',
                    $request->date_to
                )
            )
            ->orderByDesc('period_end')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $bankAccounts = BankAccount::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'university.finance.bank-reconciliations.index',
            compact(
                'reconciliations',
                'bankAccounts'
            )
        );
    }

    public function create()
    {
        $bankAccounts = BankAccount::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if ($bankAccounts->isEmpty()) {
            return redirect()
                ->route('university.bank-accounts.create')
                ->with('error', 'Crie pelo menos uma conta bancária antes de iniciar uma reconciliação.');
        }

        return view(
            'university.finance.bank-reconciliations.create',
            compact('bankAccounts')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_account_id' => [
                'required',
                'integer',
            ],

            'period_start' => [
                'required',
                'date',
            ],

            'period_end' => [
                'required',
                'date',
                'after_or_equal:period_start',
            ],

            'statement_opening_balance' => [
                'required',
                'numeric',
                'min:0',
            ],

            'statement_closing_balance' => [
                'required',
                'numeric',
                'min:0',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],
        ]);

        $bankAccount = BankAccount::query()
            ->whereKey($validated['bank_account_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $exists = BankReconciliation::query()
            ->where('bank_account_id', $bankAccount->id)
            ->where('period_start', $validated['period_start'])
            ->where('period_end', $validated['period_end'])
            ->whereIn('status', ['draft', 'reconciled'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->withErrors([
                    'period_start' => 'Já existe uma reconciliação para esta conta e período.',
                ]);
        }

        $reference = $this->generateReference();

        /*
         * Saldo do sistema no início do período.
         *
         * opening_balance
         * + movimentos anteriores ao período
         */
        $beforePeriod = BankMovement::query()
            ->where('bank_account_id', $bankAccount->id)
            ->whereDate(
                'movement_date',
                '<',
                $validated['period_start']
            )
            ->get()
            ->sum(function (BankMovement $movement) {
                return $this->movementSignedAmount($movement);
            });

        $systemOpeningBalance =
            (float) $bankAccount->opening_balance
            + $beforePeriod;

        /*
         * Movimentos até ao fim do período.
         */
        $periodMovements = BankMovement::query()
            ->where('bank_account_id', $bankAccount->id)
            ->whereDate(
                'movement_date',
                '>=',
                $validated['period_start']
            )
            ->whereDate(
                'movement_date',
                '<=',
                $validated['period_end']
            )
            ->get();

        $periodNet = $periodMovements->sum(function (BankMovement $movement) {
            return $this->movementSignedAmount($movement);
        });

        $systemClosingBalance =
            $systemOpeningBalance + $periodNet;

        $difference =
            (float) $validated['statement_closing_balance']
            - $systemClosingBalance;

        $reconciliation = BankReconciliation::create([
            'tenant_id' => $bankAccount->tenant_id,
            'bank_account_id' => $bankAccount->id,
            'created_by' => auth()->id(),
            'reference' => $reference,
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'statement_opening_balance' =>
                $validated['statement_opening_balance'],
            'statement_closing_balance' =>
                $validated['statement_closing_balance'],
            'system_opening_balance' => $systemOpeningBalance,
            'system_closing_balance' => $systemClosingBalance,
            'difference' => $difference,
            'status' => 'draft',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route(
                'university.bank-reconciliations.show',
                $reconciliation
            )
            ->with(
                'success',
                'Reconciliação criada com sucesso.'
            );
    }

    public function show(BankReconciliation $bankReconciliation)
    {
        $bankReconciliation->load([
            'bankAccount',
            'creator',
            'reconciledBy',
        ]);

        $lines = $bankReconciliation->lines()
            ->with('matchedBankMovement')
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->paginate(30);

        $pendingCount = $bankReconciliation
            ->lines()
            ->where('status', 'pending')
            ->count();

        $matchedCount = $bankReconciliation
            ->lines()
            ->where('status', 'matched')
            ->count();

        $ignoredCount = $bankReconciliation
            ->lines()
            ->where('status', 'ignored')
            ->count();

        $movements = BankMovement::query()
            ->where(
                'bank_account_id',
                $bankReconciliation->bank_account_id
            )
            ->whereDate(
                'movement_date',
                '>=',
                $bankReconciliation->period_start
            )
            ->whereDate(
                'movement_date',
                '<=',
                $bankReconciliation->period_end
            )
            ->orderByDesc('movement_date')
            ->orderByDesc('id')
            ->get();

        return view(
            'university.finance.bank-reconciliations.show',
            compact(
                'bankReconciliation',
                'lines',
                'pendingCount',
                'matchedCount',
                'ignoredCount',
                'movements'
            )
        );
    }

    public function importForm(
        BankReconciliation $bankReconciliation
    ) {
        $this->ensureDraft($bankReconciliation);

        return view(
            'university.finance.bank-reconciliations.import',
            compact('bankReconciliation')
        );
    }

    public function import(
        Request $request,
        BankReconciliation $bankReconciliation
    ) {
        $this->ensureDraft($bankReconciliation);

        $validated = $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:5120',
            ],
        ]);

        $file = $validated['file'];

        $handle = fopen(
            $file->getRealPath(),
            'r'
        );

        if ($handle === false) {
            return back()->withErrors([
                'file' => 'Não foi possível abrir o ficheiro.',
            ]);
        }

        $header = fgetcsv($handle, 0, ',');

        if (!$header) {
            fclose($handle);

            return back()->withErrors([
                'file' => 'O ficheiro CSV está vazio.',
            ]);
        }

        $header = array_map(
            fn ($value) => Str::of($value)
                ->trim()
                ->lower()
                ->replace(' ', '_')
                ->toString(),
            $header
        );

        $requiredColumns = [
            'date',
            'description',
            'type',
            'amount',
        ];

        foreach ($requiredColumns as $column) {
            if (!in_array($column, $header, true)) {
                fclose($handle);

                return back()->withErrors([
                    'file' =>
                        "O CSV deve conter a coluna obrigatória: {$column}.",
                ]);
            }
        }

        $created = 0;
        $skipped = 0;
        $errors = [];

        DB::transaction(function () use (
            $handle,
            $header,
            $bankReconciliation,
            &$created,
            &$skipped,
            &$errors
        ) {
            $lineNumber = 1;

            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $lineNumber++;

                if (
                    count($row) === 1 &&
                    trim((string) $row[0]) === ''
                ) {
                    continue;
                }

                $data = [];

                foreach ($header as $index => $column) {
                    $data[$column] = $row[$index] ?? null;
                }

                $date = trim((string) ($data['date'] ?? ''));
                $description = trim(
                    (string) ($data['description'] ?? '')
                );
                $type = strtolower(
                    trim((string) ($data['type'] ?? ''))
                );
                $amount = str_replace(
                    ',',
                    '.',
                    trim((string) ($data['amount'] ?? ''))
                );

                if (!$date || !$description) {
                    $errors[] =
                        "Linha {$lineNumber}: data ou descrição vazia.";

                    $skipped++;

                    continue;
                }

                if (!in_array($type, ['income', 'expense'], true)) {
                    $errors[] =
                        "Linha {$lineNumber}: type deve ser income ou expense.";

                    $skipped++;

                    continue;
                }

                if (
                    !is_numeric($amount) ||
                    (float) $amount <= 0
                ) {
                    $errors[] =
                        "Linha {$lineNumber}: valor inválido.";

                    $skipped++;

                    continue;
                }

                try {
                    $transactionDate =
                        \Carbon\Carbon::parse($date)->toDateString();
                } catch (\Throwable) {
                    $errors[] =
                        "Linha {$lineNumber}: data inválida.";

                    $skipped++;

                    continue;
                }

                if (
                    $transactionDate <
                    $bankReconciliation->period_start->toDateString()
                    ||
                    $transactionDate >
                    $bankReconciliation->period_end->toDateString()
                ) {
                    $errors[] =
                        "Linha {$lineNumber}: data fora do período da reconciliação.";

                    $skipped++;

                    continue;
                }

                BankStatementLine::create([
                    'tenant_id' =>
                        $bankReconciliation->tenant_id,

                    'bank_reconciliation_id' =>
                        $bankReconciliation->id,

                    'transaction_date' =>
                        $transactionDate,

                    'description' =>
                        $description,

                    'reference' =>
                        isset($data['reference'])
                            ? trim((string) $data['reference'])
                            : null,

                    'type' => $type,

                    'amount' => (float) $amount,

                    'status' => 'pending',

                    'raw_data' => $data,
                ]);

                $created++;
            }
        });

        fclose($handle);

        return redirect()
            ->route(
                'university.bank-reconciliations.show',
                $bankReconciliation
            )
            ->with(
                'success',
                "{$created} linhas importadas. {$skipped} linhas ignoradas."
            )
            ->with(
                'import_errors',
                $errors
            );
    }

    public function match(
        Request $request,
        BankReconciliation $bankReconciliation,
        BankStatementLine $line
    ) {
        $this->ensureDraft($bankReconciliation);

        $validated = $request->validate([
            'bank_movement_id' => [
                'required',
                'integer',
            ],
        ]);

        if (
            $line->bank_reconciliation_id !==
            $bankReconciliation->id
        ) {
            abort(404);
        }

        if ($line->status === 'matched') {
            return back()->withErrors([
                'bank_movement_id' =>
                    'Esta linha já está conciliada.',
            ]);
        }

        try {
            DB::transaction(function () use (
                $validated,
                $bankReconciliation,
                $line
            ) {
                $movement = BankMovement::query()
                    ->whereKey($validated['bank_movement_id'])
                    ->where(
                        'bank_account_id',
                        $bankReconciliation->bank_account_id
                    )
                    ->whereDate(
                        'movement_date',
                        '>=',
                        $bankReconciliation->period_start
                    )
                    ->whereDate(
                        'movement_date',
                        '<=',
                        $bankReconciliation->period_end
                    )
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($movement->reconciled) {
                    throw new \RuntimeException(
                        'Este movimento já foi reconciliado.'
                    );
                }

                $this->validateMovementMatch(
                    $line,
                    $movement
                );

                $line->update([
                    'matched_bank_movement_id' => $movement->id,
                    'status' => 'matched',
                    'matched_at' => now(),
                    'matched_by' => auth()->id(),
                ]);

                $movement->update([
                    'reconciled' => true,
                    'reconciled_at' => now(),
                ]);
            });
        } catch (\RuntimeException $e) {
            return back()->withErrors([
                'bank_movement_id' => $e->getMessage(),
            ]);
        }

        $this->refreshDifference($bankReconciliation);

        return back()->with(
            'success',
            'Movimento conciliado com sucesso.'
        );
    }
    
    public function unmatch(
        BankReconciliation $bankReconciliation,
        BankStatementLine $line
    ) {
        $this->ensureDraft($bankReconciliation);

        if (
            $line->bank_reconciliation_id !==
            $bankReconciliation->id
        ) {
            abort(404);
        }

        DB::transaction(function () use ($line) {
            if ($line->matched_bank_movement_id) {
                $movement = BankMovement::query()
                    ->whereKey(
                        $line->matched_bank_movement_id
                    )
                    ->lockForUpdate()
                    ->first();

                if ($movement) {
                    $movement->update([
                        'reconciled' => false,
                        'reconciled_at' => null,
                    ]);
                }
            }

            $line->update([
                'matched_bank_movement_id' => null,
                'status' => 'pending',
                'matched_at' => null,
                'matched_by' => null,
            ]);
        });

        $this->refreshDifference($bankReconciliation);

        return back()->with(
            'success',
            'Conciliação removida.'
        );
    }

    public function ignore(
        BankReconciliation $bankReconciliation,
        BankStatementLine $line
    ) {
        $this->ensureDraft($bankReconciliation);

        if (
            $line->bank_reconciliation_id !==
            $bankReconciliation->id
        ) {
            abort(404);
        }

        if ($line->status === 'matched') {
            return back()->withErrors([
                'line' =>
                    'Não é possível ignorar uma linha já conciliada.',
            ]);
        }

        $line->update([
            'status' => 'ignored',
        ]);

        return back()->with(
            'success',
            'Linha marcada como ignorada.'
        );
    }

    public function reconcile(
        BankReconciliation $bankReconciliation
    ) {
        $this->ensureDraft($bankReconciliation);

        $pending = $bankReconciliation
            ->lines()
            ->where('status', 'pending')
            ->count();

        if ($pending > 0) {
            return back()->withErrors([
                'reconciliation' =>
                    "Existem {$pending} linhas pendentes. Concilie ou ignore todas antes de concluir.",
            ]);
        }

        $this->refreshDifference($bankReconciliation);

        $bankReconciliation->refresh();

        if (abs((float) $bankReconciliation->difference) > 0.01) {
            return back()->withErrors([
                'reconciliation' =>
                    'A reconciliação não pode ser concluída enquanto existir diferença entre o saldo do sistema e o saldo do extrato.',
            ]);
        }

        $bankReconciliation->update([
            'status' => 'reconciled',
            'reconciled_at' => now(),
            'reconciled_by' => auth()->id(),
        ]);

        return redirect()
            ->route(
                'university.bank-reconciliations.show',
                $bankReconciliation
            )
            ->with(
                'success',
                'Reconciliação bancária concluída com sucesso.'
            );
    }

    private function ensureDraft(
        BankReconciliation $bankReconciliation
    ): void {
        if (!$bankReconciliation->isDraft()) {
            abort(
                422,
                'Esta reconciliação já foi concluída.'
            );
        }
    }

    private function generateReference(): string
    {
        do {
            $reference =
                'REC-' .
                now()->format('Ym') .
                '-' .
                strtoupper(Str::random(6));
        } while (
            BankReconciliation::query()
                ->where('reference', $reference)
                ->exists()
        );

        return $reference;
    }

    private function movementSignedAmount(
        BankMovement $movement
    ): float {
        if (
            $movement->type === 'income' ||
            $movement->type === 'transfer_in'
        ) {
            return (float) $movement->amount;
        }

        if (
            $movement->type === 'expense' ||
            $movement->type === 'transfer_out'
        ) {
            return -(float) $movement->amount;
        }

        if (
            $movement->type === 'adjustment'
        ) {
            return $movement->adjustment_direction === 'positive'
                ? (float) $movement->amount
                : -(float) $movement->amount;
        }

        return 0;
    }

    private function validateMovementMatch(
        BankStatementLine $line,
        BankMovement $movement
    ): void {
        $lineType = $line->type;

        $movementIsIncome =
            $movement->isIncome();

        $movementIsExpense =
            $movement->isExpense();

        if (
            $lineType === 'income' &&
            !$movementIsIncome
        ) {
            throw new \RuntimeException(
                'Uma entrada do extrato só pode ser conciliada com uma entrada bancária.'
            );
        }

        if (
            $lineType === 'expense' &&
            !$movementIsExpense
        ) {
            throw new \RuntimeException(
                'Uma saída do extrato só pode ser conciliada com uma saída bancária.'
            );
        }

        if (
            abs(
                (float) $line->amount -
                (float) $movement->amount
            ) > 0.01
        ) {
            throw new \RuntimeException(
                'O valor do extrato não corresponde ao valor do movimento bancário.'
            );
        }
    }

    private function refreshDifference(
        BankReconciliation $bankReconciliation
    ): void {
        $bankReconciliation->refresh();

        /*
         * A diferença principal da reconciliação é:
         *
         * saldo do extrato - saldo calculado pelo sistema.
         */
        $difference =
            (float) $bankReconciliation->statement_closing_balance
            -
            (float) $bankReconciliation->system_closing_balance;

        $bankReconciliation->update([
            'difference' => $difference,
        ]);
    }
}
