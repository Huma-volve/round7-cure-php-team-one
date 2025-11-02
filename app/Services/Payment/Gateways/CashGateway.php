<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\Payment\PaymentGatewayInterface;
use App\DTOs\Payment\CreatePaymentDTO;
use App\DTOs\Payment\PaymentResponseDTO;
use App\DTOs\Payment\WebhookPayloadDTO;

class CashGateway implements PaymentGatewayInterface
{
    public function createPayment(CreatePaymentDTO $payload): PaymentResponseDTO
    {
        return new PaymentResponseDTO(
            successful: true,
            provider: 'cash',
            paymentId: null,
            clientSecret: null,
            approveUrl: null,
            status: 'pending',
            raw: ['note' => 'Cash payments handled offline on appointment']
        );
    }

    public function confirm(string $paymentId, array $context = []): PaymentResponseDTO
    {
        return new PaymentResponseDTO(true, 'cash', null, null, null, 'pending', []);
    }

    public function handleWebhook(WebhookPayloadDTO $payload): PaymentResponseDTO
    {
        return new PaymentResponseDTO(true, 'cash', null, null, null, 'pending', []);
    }
}
