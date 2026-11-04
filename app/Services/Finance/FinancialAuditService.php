<?php

namespace App\Services\Finance;

use App\Models\FinancialAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FinancialAuditService
{
    public function record(
        string $module,
        string $event,
        string $description,
        ?Model $record = null,
        array $oldValues = [],
        array $newValues = [],
        array $metadata = []
    ): FinancialAuditLog {
        $user = Auth::user();

        if (!$user || !$user->tenant_id) {
            throw new \RuntimeException(
                'Não foi possível identificar a universidade para registar a auditoria.'
            );
        }

        return FinancialAuditLog::create([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'module' => $module,
            'event' => $event,
            'auditable_type' => $record?->getMorphClass(),
            'auditable_id' => $record?->getKey(),
            'description' => $description,

            'old_values' => $this->sanitize($oldValues),
            'new_values' => $this->sanitize($newValues),
            'metadata' => $this->sanitize($metadata),

            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Remove dados sensíveis de qualquer estrutura de dados.
     */
    protected function sanitize(array $data): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'client_secret',
            'api_secret',
            'webhook_secret',
            'access_token',
            'refresh_token',
            'credentials',
            'raw_payload',
            'secret',
            'token',
            'authorization',
            'private_key',
            'certificate',
        ];

        $clean = [];

        foreach ($data as $key => $value) {
            $normalizedKey = strtolower((string) $key);

            $isSensitive = collect($sensitiveKeys)->contains(
                fn ($sensitiveKey) =>
                    $normalizedKey === $sensitiveKey
                    || str_contains($normalizedKey, $sensitiveKey)
            );

            if ($isSensitive) {
                $clean[$key] = '[DADO PROTEGIDO]';
                continue;
            }

            if (is_array($value)) {
                $clean[$key] = $this->sanitize($value);
                continue;
            }

            if (is_object($value)) {
                $clean[$key] = '[OBJECTO OMITIDO]';
                continue;
            }

            if (is_string($value) && strlen($value) > 10000) {
                $clean[$key] = '[CONTEÚDO OMITIDO]';
                continue;
            }

            $clean[$key] = $value;
        }

        return $clean;
    }


}
