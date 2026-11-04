<?php

namespace App\Services\Banking\Providers;

use App\Models\BankIntegration;
use Illuminate\Http\Request;

class GenericBankProvider extends AbstractBankProvider
{
    public function testConnection(BankIntegration $integration): array
    {
        $this->notImplemented('teste de ligação bancária');
    }

    public function fetchTransactions(
        BankIntegration $integration,
        ?string $from = null,
        ?string $to = null,
        ?string $cursor = null
    ): array {
        $this->notImplemented('consulta de movimentos bancários');
    }

    public function createPaymentReference(
        BankIntegration $integration,
        array $data
    ): array {
        $this->notImplemented('criação de referência bancária');
    }

    public function verifyWebhook(
        BankIntegration $integration,
        Request $request
    ): bool {
        $this->notImplemented('validação de webhook');
    }

    public function parseWebhook(
        BankIntegration $integration,
        Request $request
    ): array {
        $this->notImplemented('interpretação de webhook');
    }
}
