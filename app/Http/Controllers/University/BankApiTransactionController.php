<?php

namespace App\Http\Controllers\University;

use App\Http\Controllers\Controller;
use App\Services\Banking\BankPaymentConfirmationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class BankApiTransactionController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = auth()->user()->tenant_id;

        $query = DB::table('bank_api_transactions as t')
            ->join('bank_integrations as i', function ($join) use ($tenantId) {
                $join->on('i.id', '=', 't.bank_integration_id')
                    ->where('i.tenant_id', $tenantId);
            })
            ->join('bank_accounts as a', function ($join) use ($tenantId) {
                $join->on('a.id', '=', 't.bank_account_id')
                    ->where('a.tenant_id', $tenantId);
            })
            ->where('t.tenant_id', $tenantId)
            ->select([
                't.*',
                'i.name as integration_name',
                'i.provider',
                'a.name as bank_account_name',
            ]);

        if ($request->filled('bank_account_id')) {
            $query->where('t.bank_account_id', $request->bank_account_id);
        }

        if ($request->filled('status')) {
            $query->where('t.status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('t.type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('t.occurred_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('t.occurred_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = '%' . trim($request->search) . '%';

            $query->where(function ($q) use ($search) {
                $q->where('t.reference', 'ILIKE', $search)
                    ->orWhere('t.external_transaction_id', 'ILIKE', $search)
                    ->orWhere('t.description', 'ILIKE', $search);
            });
        }

        $transactions = $query
            ->orderByDesc('t.occurred_at')
            ->orderByDesc('t.id')
            ->paginate(20)
            ->withQueryString();

        $accounts = DB::table('bank_accounts')
            ->where('tenant_id', $tenantId)
            ->orderBy('name')
            ->get();

        // $summary = DB::table('bank_api_transactions')
        //     ->where('tenant_id', $tenantId)
        //     ->selectRaw("
        //         COUNT(*) as total,
        //         COUNT(*) FILTER (WHERE status = 'confirmed') as confirmed,
        //         COUNT(*) FILTER (WHERE status = 'unmatched') as unmatched,
        //         COUNT(*) FILTER (WHERE status = 'failed') as failed
        //     ")
        //     ->first();

        $summary = DB::table('bank_api_transactions')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = 'unmatched' THEN 1 ELSE 0 END) as unmatched,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
            ")
            ->where('tenant_id', $tenantId)
            ->first();

        return view('university.finance.bank-api-transactions.index', compact(
            'transactions',
            'accounts',
            'summary'
        ));
    }

    public function show(int $transaction)
    {
        $tenantId = auth()->user()->tenant_id;

        $bankTransaction = DB::table('bank_api_transactions as t')
            ->join('bank_integrations as i', function ($join) use ($tenantId) {
                $join->on('i.id', '=', 't.bank_integration_id')
                    ->where('i.tenant_id', $tenantId);
            })
            ->join('bank_accounts as a', function ($join) use ($tenantId) {
                $join->on('a.id', '=', 't.bank_account_id')
                    ->where('a.tenant_id', $tenantId);
            })
            ->leftJoin('bank_movements as m', function ($join) use ($tenantId) {
                $join->on('m.id', '=', 't.bank_movement_id')
                    ->where('m.tenant_id', $tenantId);
            })
            ->where('t.tenant_id', $tenantId)
            ->where('t.id', $transaction)
            ->select([
                't.*',
                'i.name as integration_name',
                'i.provider',
                'a.name as bank_account_name',
                'm.reference as movement_reference',
            ])
            ->first();

        abort_if(!$bankTransaction, 404);

        $payload = is_array($bankTransaction->raw_payload)
            ? $bankTransaction->raw_payload
            : json_decode($bankTransaction->raw_payload ?? '{}', true);

        return view('university.finance.bank-api-transactions.show', compact(
            'bankTransaction',
            'payload'
        ));
    }

    public function confirm(
        int $transaction,
        BankPaymentConfirmationService $service
    ) {
        $tenantId = auth()->user()->tenant_id;

        $exists = DB::table('bank_api_transactions')
            ->where('tenant_id', $tenantId)
            ->where('id', $transaction)
            ->exists();

        abort_unless($exists, 404);

        try {
            $result = $service->confirm($tenantId, $transaction);

            if ($result['status'] === 'confirmed') {
                return redirect()
                    ->route('university.bank-api-transactions.show', $transaction)
                    ->with('success', $result['message']);
            }

            return redirect()
                ->route('university.bank-api-transactions.show', $transaction)
                ->with('warning', $result['message']);

        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'transaction' => $exception->getMessage(),
            ]);
        }
    }


    public function associateForm(int $transaction)
    {
        $tenantId = auth()->user()->tenant_id;

        $bankTransaction = DB::table('bank_api_transactions')
            ->where('tenant_id', $tenantId)
            ->where('id', $transaction)
            ->first();

        abort_if(!$bankTransaction, 404);

        if (
            $bankTransaction->type !== 'income' ||
            $bankTransaction->status === 'confirmed'
        ) {
            return back()->withErrors([
                'transaction' => 'Esta transacção não pode ser associada manualmente.',
            ]);
        }

        $invoices = DB::table('invoices')
            ->where('tenant_id', $tenantId)
            ->whereNotIn('status', ['draft', 'cancelled', 'paid'])
            ->where('balance', '>', 0)
            ->orderByDesc('created_at')
            ->get([
                'id',
                'invoice_number',
                'total',
                'balance',
                'currency',
                'status',
            ]);

        return view(
            'university.finance.bank-api-transactions.associate',
            compact('bankTransaction', 'invoices')
        );
    }

    public function associate(Request $request, int $transaction,  BankPaymentConfirmationService $service)
    {
        $request->validate([
            'invoice_id' => ['required', 'integer'],
            'observations' => ['nullable', 'string', 'max:1000'],
        ]);

        $tenantId = auth()->user()->tenant_id;

        try {
            DB::transaction(function () use ($request, $tenantId, $transaction) {
                $bankTransaction = DB::table('bank_api_transactions')
                    ->where('tenant_id', $tenantId)
                    ->where('id', $transaction)
                    ->lockForUpdate()
                    ->first();

                if (!$bankTransaction) {
                    abort(404);
                }

                if (
                    $bankTransaction->type !== 'income' ||
                    $bankTransaction->status === 'confirmed'
                ) {
                    throw new \RuntimeException(
                        'Esta transacção não pode ser associada.'
                    );
                }

                $invoice = DB::table('invoices')
                    ->where('tenant_id', $tenantId)
                    ->where('id', $request->invoice_id)
                    ->lockForUpdate()
                    ->first();

                if (!$invoice) {
                    throw new \RuntimeException(
                        'Factura não encontrada nesta universidade.'
                    );
                }

                if (in_array($invoice->status, ['draft', 'cancelled', 'paid'], true)) {
                    throw new \RuntimeException(
                        'A factura não está disponível para receber pagamentos.'
                    );
                }

                $amount = round((float) $bankTransaction->amount, 2);
                $balance = round((float) $invoice->balance, 2);

                if ($amount <= 0 || $amount > $balance) {
                    throw new \RuntimeException(
                        'O valor da transferência é superior ao saldo da factura ou inválido.'
                    );
                }

                if (
                    strtoupper($bankTransaction->currency) !==
                    strtoupper($invoice->currency)
                ) {
                    throw new \RuntimeException(
                        'A moeda da transferência é diferente da moeda da factura.'
                    );
                }

                // Não permitir que a mesma referência seja usada noutra factura.
                $existingReference = DB::table('bank_payment_references')
                    ->where('tenant_id', $tenantId)
                    ->where('reference', $bankTransaction->reference)
                    ->first();

                if ($existingReference) {
                    if (
                        (int) $existingReference->invoice_id !== (int) $invoice->id ||
                        $existingReference->status === 'paid'
                    ) {
                        throw new \RuntimeException(
                            'Esta referência já está associada a outro pagamento.'
                        );
                    }

                    throw new \RuntimeException(
                        'Já existe uma referência de pagamento para esta transacção.'
                    );
                }

                DB::table('bank_payment_references')->insert([
                    'tenant_id' => $tenantId,
                    'bank_integration_id' => $bankTransaction->bank_integration_id,
                    'invoice_id' => $invoice->id,
                    'reference' => $bankTransaction->reference,
                    'external_reference' => $bankTransaction->external_transaction_id,
                    'amount' => $amount,
                    'currency' => strtoupper($bankTransaction->currency),
                    'status' => 'pending',
                    'expires_at' => null,
                    'paid_at' => null,
                    'metadata' => json_encode([
                        'source' => 'manual_association',
                        'observations' => $request->observations,
                        'associated_by' => auth()->id(),
                        'associated_at' => now()->toISOString(),
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('bank_api_transactions')
                    ->where('id', $bankTransaction->id)
                    ->where('tenant_id', $tenantId)
                    ->update([
                        'status' => 'matched',
                        'updated_at' => now(),
                    ]);
            }, 3);


            $result = $service->confirm($tenantId, $transaction);

            return redirect()
                ->route('university.bank-api-transactions.show', $transaction)
                ->with(
                    $result['status'] === 'confirmed' ? 'success' : 'warning',
                    $result['message']
                );

            return redirect()
                ->route('university.bank-api-transactions.confirm', [
                    'transaction' => $transaction,
                ])
                ->with('success', 'Transacção associada à factura. Já pode confirmar o pagamento.');

        } catch (\Throwable $exception) {
            report($exception);

            return back()->withInput()->withErrors([
                'association' => $exception->getMessage(),
            ]);
        }
    }

}
