<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\BankIntegration;
use App\Services\Banking\BankIntegrationManager;
use App\Services\Banking\Exceptions\BankIntegrationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Jobs\ProcessBankWebhookJob;
use Throwable;

class BankWebhookController extends Controller
{
    public function __invoke(
        Request $request,
        string $publicId,
        BankIntegrationManager $manager
    ) {
        // O webhook é público, por isso não depende do middleware de tenant.
        // O public_id identifica a integração sem expor o ID interno.
        $integration = BankIntegration::withoutGlobalScopes()
            ->where('public_id', $publicId)
            ->firstOrFail();

        if ($integration->status !== 'active') {
            return response()->json([
                'message' => 'Integração bancária inactiva.',
            ], 403);
        }

        try {
            // A assinatura deve ser validada antes de confiar no conteúdo.
            $signatureValid = $manager->verifyWebhook($integration, $request);

            if (!$signatureValid) {
                $this->recordWebhook( $integration, null, null, $request->all(), false, 'rejected', 'Assinatura do webhook inválida.');

                return response()->json([
                    'message' => 'Assinatura inválida.',
                ], 401);
            }

            $event = $manager->parseWebhook($integration, $request);

            $externalEventId = $event['external_event_id'] ?? null;
            $eventType = $event['event_type'] ?? null;
            $payload = $event['payload'] ?? $request->all();

            if (!$externalEventId || !$eventType || !is_array($payload)) {
                $this->recordWebhook(
                    $integration,
                    null,
                    $eventType,
                    $request->all(),
                    true,
                    'invalid_payload',
                    'Formato de notificação bancária inválido.'
                );

                return response()->json([
                    'message' => 'Formato de notificação inválido.',
                ], 422);
            }

            $existing = DB::table('bank_webhook_logs')
                ->where('tenant_id', $integration->tenant_id)
                ->where('bank_integration_id', $integration->id)
                ->where('external_event_id', $externalEventId)
                ->first();

            if ($existing) {
                return response()->json([
                    'message' => 'Notificação já recebida.',
                    'duplicate' => true,
                ], 200);
            }

            $inserted = $this->recordWebhook(
                $integration,
                $externalEventId,
                $eventType,
                $payload,
                true,
                'received'
            );

            if (!$inserted) {
                return response()->json([
                    'message' => 'Notificação já recebida.',
                    'duplicate' => true,
                ], 200);
            }

            $webhookLog = DB::table('bank_webhook_logs')
                ->where('tenant_id', $integration->tenant_id)
                ->where('bank_integration_id', $integration->id)
                ->where('external_event_id', $externalEventId)
                ->first();

            ProcessBankWebhookJob::dispatch(
                $webhookLog->id,
                $integration->tenant_id
            )->afterCommit();

            return response()->json([
                'message' => 'Notificação recebida e enviada para processamento.',
            ], 202);

        } catch (BankIntegrationException $exception) {
            // Os adaptadores ainda não implementados não podem aceitar
            // notificações como se a assinatura estivesse validada.
            $this->recordWebhook(
                $integration,
                null,
                null,
                $request->all(),
                false,
                'unsupported',
                $exception->getMessage()
            );

            return response()->json([
                'message' => 'Validação de webhook ainda não disponível para este banco.',
            ], 503);

        } catch (Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Não foi possível processar a notificação.',
            ], 500);
        }
    }

    private function recordWebhook(
        BankIntegration $integration,
        ?string $externalEventId,
        ?string $eventType,
        array $payload,
        bool $signatureValid,
        string $status,
        ?string $errorMessage = null
    ): bool {
        return DB::table('bank_webhook_logs')->insertOrIgnore([
            'tenant_id' => $integration->tenant_id,
            'bank_integration_id' => $integration->id,
            'external_event_id' => $externalEventId,
            'event_type' => $eventType,
            'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'signature_valid' => $signatureValid,
            'status' => $status,
            'attempts' => 1,
            'received_at' => now(),
            'processed_at' => null,
            'error_message' => $errorMessage
                ? Str::limit($errorMessage, 1000)
                : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]) > 0;
    }
}
