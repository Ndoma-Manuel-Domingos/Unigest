<?php

namespace App\Services\Banking;

use App\Models\BankIntegration;
use App\Services\Banking\Contracts\BankProviderInterface;
use App\Services\Banking\Exceptions\BankIntegrationException;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

class BankIntegrationManager
{
    // public function provider(BankIntegration $integration): BankProviderInterface 
    // {

    //     $providers = config('banking.providers', []);

    //     $providerClass = $providers[$integration->provider] ?? null;

    //     if (!$providerClass) {
    //         throw new BankIntegrationException(
    //             'O provedor bancário não está registado.'
    //         );
    //     }

    //     $provider = App::make($providerClass);

    //     if (!$provider instanceof BankProviderInterface) {
    //         throw new BankIntegrationException(
    //             'O provedor bancário não implementa a interface necessária.'
    //         );
    //     }

    //     return $provider;
    // }

    public function testConnection(
        BankIntegration $integration
    ): array {
        return $this->provider($integration)
            ->testConnection($integration);
    }

    public function fetchTransactions(
        BankIntegration $integration,
        ?string $from = null,
        ?string $to = null,
        ?string $cursor = null
    ): array {
        return $this->provider($integration)
            ->fetchTransactions(
                $integration,
                $from,
                $to,
                $cursor
            );
    }

    public function createPaymentReference(
        BankIntegration $integration,
        array $data
    ): array {
        return $this->provider($integration)
            ->createPaymentReference($integration, $data);
    }


    public function verifyWebhook(
        BankIntegration $integration,
        Request $request
    ): bool {
        $provider = $this->provider($integration);

        return $provider->verifyWebhook($integration, $request);
    }

    public function parseWebhook(
        BankIntegration $integration,
        Request $request
    ): array {
        $provider = $this->provider($integration);

        return $provider->parseWebhook($integration, $request);
    }

    private function provider(BankIntegration $integration): BankProviderInterface
    {
        $providerClass = config(
            'banking.providers.' . $integration->provider
        );

        if (!$providerClass || !class_exists($providerClass)) {
            throw new BankIntegrationException(
                'Fornecedor bancário não configurado.'
            );
        }

        $provider = app($providerClass);

        if (!$provider instanceof BankProviderInterface) {
            throw new BankIntegrationException(
                'Adaptador bancário inválido.'
            );
        }

        return $provider;
    }

}
