<?php

namespace App\Services\Banking\Providers;

use App\Models\BankIntegration;
use Illuminate\Http\Request;

class BaiBankProvider extends AbstractBankProvider
{
    public function testConnection(BankIntegration $integration): array
    {
        $this->notImplemented('teste de ligação BAI');
    }

    public function fetchTransactions(
        BankIntegration $integration,
        ?string $from = null,
        ?string $to = null,
        ?string $cursor = null
    ): array {
        $this->notImplemented('consulta de movimentos BAI');
    }

    public function createPaymentReference(
        BankIntegration $integration,
        array $data
    ): array {
        $this->notImplemented('criação de referência BAI');
    }

    public function verifyWebhook(
        BankIntegration $integration,
        Request $request
    ): bool {
        $this->notImplemented('validação de webhook BAI');
    }

    public function parseWebhook(
        BankIntegration $integration,
        Request $request
    ): array {
        $this->notImplemented('interpretação de webhook BAI');
    }
}
