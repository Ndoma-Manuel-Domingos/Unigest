<?php

namespace App\Services\Banking;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

class BankPaymentConfirmationService
{
    public function confirm(int $tenantId, int $transactionId): array
    {
        return DB::transaction(function () use ($tenantId, $transactionId) {

            $transaction = DB::table('bank_api_transactions')
                ->where('id', $transactionId)
                ->where('tenant_id', $tenantId)
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                throw new RuntimeException('Transacção bancária não encontrada.');
            }

            // Uma transacção já confirmada nunca volta a criar pagamento.
            if ($transaction->status === 'confirmed') {
                return [
                    'status' => 'already_confirmed',
                    'message' => 'Esta transacção já foi confirmada.',
                ];
            }

            $integration = DB::table('bank_integrations')
                ->where('id', $transaction->bank_integration_id)
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$integration || $integration->status !== 'active') {
                throw new RuntimeException('Integração bancária inválida ou inactiva.');
            }

            $reference = DB::table('bank_payment_references')
                ->where('tenant_id', $tenantId)
                ->where('bank_integration_id', $integration->id)
                ->where(function ($query) use ($transaction) {
                    $query->where('reference', $transaction->reference)
                        ->orWhere('external_reference', $transaction->reference);
                })
                ->lockForUpdate()
                ->first();

            if (!$reference) {
                DB::table('bank_api_transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'status' => 'unmatched',
                        'updated_at' => now(),
                    ]);

                return [
                    'status' => 'unmatched',
                    'message' => 'Não existe referência de pagamento correspondente.',
                ];
            }

            if ($reference->status === 'paid') {
                throw new RuntimeException('Esta referência bancária já foi utilizada.');
            }

            if ($reference->expires_at && now()->gt($reference->expires_at)) {
                DB::table('bank_payment_references')
                    ->where('id', $reference->id)
                    ->update([
                        'status' => 'expired',
                        'updated_at' => now(),
                    ]);

                throw new RuntimeException('A referência bancária expirou.');
            }

            $amount = round((float) $transaction->amount, 2);
            $referenceAmount = round((float) $reference->amount, 2);

            if ($amount !== $referenceAmount) {
                throw new RuntimeException(
                    'O valor recebido não corresponde ao valor da referência.'
                );
            }

            if (
                strtoupper($transaction->currency) !==
                strtoupper($reference->currency)
            ) {
                throw new RuntimeException(
                    'A moeda recebida não corresponde à moeda da referência.'
                );
            }

            if ($transaction->type !== 'income') {
                throw new RuntimeException(
                    'A transacção bancária não é uma entrada de dinheiro.'
                );
            }

            $invoice = DB::table('invoices')
                ->where('id', $reference->invoice_id)
                ->where('tenant_id', $tenantId)
                ->lockForUpdate()
                ->first();

            if (!$invoice) {
                throw new RuntimeException('Factura não encontrada nesta universidade.');
            }

            if (in_array($invoice->status, ['draft', 'cancelled'], true)) {
                throw new RuntimeException(
                    'Não é permitido receber pagamentos para facturas em rascunho ou canceladas.'
                );
            }

            $confirmedPayments = DB::table('payments')
                ->where('tenant_id', $tenantId)
                ->where('invoice_id', $invoice->id)
                ->where('status', 'confirmed')
                ->sum('amount');

            $balance = round(
                (float) $invoice->total - (float) $confirmedPayments,
                2
            );

            if ($amount > $balance) {
                throw new RuntimeException(
                    'O valor recebido ultrapassa o saldo em dívida da factura.'
                );
            }

            $bankAccount = DB::table('bank_accounts')
                ->where('id', $integration->bank_account_id)
                ->where('tenant_id', $tenantId)
                ->lockForUpdate()
                ->first();

            if (!$bankAccount || !$bankAccount->is_active) {
                throw new RuntimeException('A conta bancária está inactiva ou não existe.');
            }

            if (
                strtoupper($bankAccount->currency) !==
                strtoupper($transaction->currency)
            ) {
                throw new RuntimeException(
                    'A moeda da conta bancária não corresponde à transacção.'
                );
            }

            $paymentNumber = $this->generatePaymentNumber($tenantId);

            $paymentDate = $transaction->occurred_at
                ? date('Y-m-d', strtotime($transaction->occurred_at))
                : now()->toDateString();

            $paymentId = DB::table('payments')->insertGetId([
                'tenant_id' => $tenantId,
                'invoice_id' => $invoice->id,
                'payment_number' => $paymentNumber,
                'payment_date' => $paymentDate,
                'amount' => $amount,
                'currency' => strtoupper($transaction->currency),
                'payment_method' => 'bank_transfer',
                'transaction_reference' => $transaction->external_transaction_id,
                'status' => 'confirmed',
                'observations' => 'Pagamento confirmado automaticamente pela integração bancária.',
                'received_by' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Registar a entrada na conta bancária da universidade.
            $movementId = DB::table('bank_movements')->insertGetId([
                'tenant_id' => $tenantId,
                'bank_account_id' => $bankAccount->id,
                'user_id' => null,
                'type' => 'income',
                'reference' => $paymentNumber,
                'description' => 'Pagamento automático da factura ' . $invoice->invoice_number,
                'amount' => $amount,
                'movement_date' => $paymentDate,
                'document_number' => $invoice->invoice_number,
                'transaction_reference' => $transaction->external_transaction_id,
                'reconciled' => false,
                'reconciled_at' => null,
                'observations' => 'Origem: integração bancária automática.',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('bank_accounts')
                ->where('id', $bankAccount->id)
                ->where('tenant_id', $tenantId)
                ->update([
                    'current_balance' => DB::raw('current_balance + ' . $amount),
                    'updated_at' => now(),
                ]);

            DB::table('bank_payment_references')
                ->where('id', $reference->id)
                ->where('tenant_id', $tenantId)
                ->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                    'updated_at' => now(),
                ]);

            DB::table('bank_api_transactions')
                ->where('id', $transaction->id)
                ->where('tenant_id', $tenantId)
                ->update([
                    'bank_movement_id' => $movementId,
                    'status' => 'confirmed',
                    'updated_at' => now(),
                ]);

            // Recalcular os valores da factura a partir dos pagamentos confirmados.
            $newAmountPaid = round(
                (float) $confirmedPayments + $amount,
                2
            );

            $newBalance = max(
                round((float) $invoice->total - $newAmountPaid, 2),
                0
            );

            $newStatus = $newBalance <= 0
                ? 'paid'
                : 'partially_paid';

            DB::table('invoices')
                ->where('id', $invoice->id)
                ->where('tenant_id', $tenantId)
                ->update([
                    'amount_paid' => $newAmountPaid,
                    'balance' => $newBalance,
                    'status' => $newStatus,
                    'updated_at' => now(),
                ]);

            return [
                'status' => 'confirmed',
                'payment_id' => $paymentId,
                'bank_movement_id' => $movementId,
                'invoice_id' => $invoice->id,
                'message' => 'Pagamento bancário confirmado com sucesso.',
            ];
        }, 3);
    }

    private function generatePaymentNumber(int $tenantId): string
    {
        do {
            $number = 'BAN-' . now()->format('Ymd') . '-' .
                Str::upper(Str::random(6));

            $exists = DB::table('payments')
                ->where('tenant_id', $tenantId)
                ->where('payment_number', $number)
                ->exists();

        } while ($exists);

        return $number;
    }
}
