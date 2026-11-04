<?php

namespace App\Services\Banking\Providers;

use App\Models\BankIntegration;
use App\Services\Banking\Contracts\BankProviderInterface;
use App\Services\Banking\Exceptions\BankIntegrationException;
use Illuminate\Http\Request;

abstract class AbstractBankProvider implements BankProviderInterface
{
    protected function getCredentials(BankIntegration $integration): array
    {
        $credentials = $integration->credentials;

        if (!$credentials) {
            throw new BankIntegrationException(
                'As credenciais bancárias ainda não foram configuradas.'
            );
        }

        return $credentials->credentials ?? [];
    }

    protected function requireCredential(
        array $credentials,
        string $key
    ): string {
        $value = $credentials[$key] ?? null;

        if (!is_string($value) || trim($value) === '') {
            throw new BankIntegrationException(
                "A credencial {$key} não está configurada."
            );
        }

        return $value;
    }

    protected function requireBaseUrl(BankIntegration $integration): string
    {
        if (!$integration->base_url) {
            throw new BankIntegrationException(
                'A URL da API bancária não foi configurada.'
            );
        }

        return rtrim($integration->base_url, '/');
    }

    protected function ensureActive(BankIntegration $integration): void
    {
        if (!$integration->isActive()) {
            throw new BankIntegrationException(
                'Esta integração bancária não está activa.'
            );
        }
    }

    protected function notImplemented(string $operation): never
    {
        throw new BankIntegrationException(
            "A operação {$operation} ainda não foi implementada para este banco."
        );
    }
}
