<?php

namespace App\Jobs;

use App\Services\Banking\BankPaymentConfirmationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class ProcessBankWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [30, 60, 180, 300];

    public function __construct(
        public int $webhookLogId,
        public int $tenantId
    ) {}

    public function handle(
        BankPaymentConfirmationService $confirmationService
    ): void {
        $log = DB::table('bank_webhook_logs')
            ->where('id', $this->webhookLogId)
            ->where('tenant_id', $this->tenantId)
            ->first();

        if (!$log || !$log->signature_valid) {
            return;
        }

        if (in_array($log->status, ['processed', 'ignored'], true)) {
            return;
        }

        try {
            $transactionId = DB::transaction(function () use ($log) {
                $integration = DB::table('bank_integrations')
                    ->where('id', $log->bank_integration_id)
                    ->where('tenant_id', $this->tenantId)
                    ->lockForUpdate()
                    ->first();

                if (!$integration || $integration->status !== 'active') {
                    throw new RuntimeException(
                        'Integração bancária inexistente ou inactiva.'
                    );
                }

                $payload = is_array($log->payload)
                    ? $log->payload
                    : json_decode($log->payload ?? '{}', true);

                if (!is_array($payload)) {
                    throw new RuntimeException('Payload inválido.');
                }

                $externalId = $payload['external_transaction_id'] ?? null;
                $reference = $payload['reference'] ?? null;
                $type = $payload['type'] ?? null;
                $amount = $payload['amount'] ?? null;
                $currency = strtoupper($payload['currency'] ?? '');

                if (
                    !$externalId ||
                    !$reference ||
                    !in_array($type, ['income', 'expense'], true) ||
                    !is_numeric($amount) ||
                    (float) $amount <= 0 ||
                    !preg_match('/^[A-Z]{3}$/', $currency)
                ) {
                    throw new RuntimeException(
                        'Dados da transacção bancária inválidos.'
                    );
                }

                $existing = DB::table('bank_api_transactions')
                    ->where('tenant_id', $this->tenantId)
                    ->where('bank_integration_id', $integration->id)
                    ->where('external_transaction_id', $externalId)
                    ->lockForUpdate()
                    ->first();

                if ($existing) {
                    return $existing->id;
                }

                return DB::table('bank_api_transactions')->insertGetId([
                    'tenant_id' => $this->tenantId,
                    'bank_integration_id' => $integration->id,
                    'bank_account_id' => $integration->bank_account_id,
                    'bank_movement_id' => null,
                    'external_transaction_id' => $externalId,
                    'reference' => $reference,
                    'description' => $payload['description'] ?? 'Notificação bancária',
                    'type' => $type,
                    'amount' => $amount,
                    'currency' => $currency,
                    'occurred_at' => $payload['occurred_at'] ?? now(),
                    'status' => 'unmatched',
                    'raw_payload' => json_encode(
                        $payload,
                        JSON_UNESCAPED_UNICODE
                    ),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }, 3);

            $result = $confirmationService->confirm(
                $this->tenantId,
                $transactionId
            );

            DB::table('bank_webhook_logs')
                ->where('id', $log->id)
                ->where('tenant_id', $this->tenantId)
                ->update([
                    'status' => 'processed',
                    'processed_at' => now(),
                    'error_message' => $result['status'] === 'unmatched'
                        ? $result['message']
                        : null,
                    'updated_at' => now(),
                ]);

        } catch (Throwable $exception) {
            DB::table('bank_webhook_logs')
                ->where('id', $log->id)
                ->where('tenant_id', $this->tenantId)
                ->update([
                    'status' => 'failed',
                    'error_message' => mb_substr(
                        $exception->getMessage(),
                        0,
                        1000
                    ),
                    'updated_at' => now(),
                ]);

            throw $exception;
        }
    }
}
