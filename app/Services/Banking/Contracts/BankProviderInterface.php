<?php

namespace App\Services\Banking\Contracts;

use App\Models\BankIntegration;
use Illuminate\Http\Request;

interface BankProviderInterface
{
    /**
     * Testar a ligação com o banco.
     */
    public function testConnection(BankIntegration $integration): array;

    /**
     * Consultar movimentos bancários.
     */
    public function fetchTransactions(
        BankIntegration $integration,
        ?string $from = null,
        ?string $to = null,
        ?string $cursor = null
    ): array;

    /**
     * Criar uma referência de pagamento.
     */
    public function createPaymentReference(
        BankIntegration $integration,
        array $data
    ): array;

    /**
     * Validar a autenticidade de uma notificação.
     */
    public function verifyWebhook(
        BankIntegration $integration,
        Request $request
    ): bool;

    /**
     * Interpretar uma notificação recebida.
     */
    public function parseWebhook(
        BankIntegration $integration,
        Request $request
    ): array;
}
