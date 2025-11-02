<?php

namespace App\Services\Payment;

use App\DTOs\Payment\CreatePaymentDTO;
use App\DTOs\Payment\PaymentResponseDTO;
use App\DTOs\Payment\WebhookPayloadDTO;

class PaymentService
{
    public function __construct(private readonly PaymentFactory $factory)
    {
    }

    public function create(CreatePaymentDTO $payload): PaymentResponseDTO
    {
        $gateway = $this->factory->make($payload->gateway);
        return $gateway->createPayment($payload);
    }

    public function confirm(string $gateway, string $paymentId, array $context = []): PaymentResponseDTO
    {
        $g = $this->factory->make($gateway);
        return $g->confirm($paymentId, $context);
    }

    public function webhook(WebhookPayloadDTO $payload): PaymentResponseDTO
    {
        $g = $this->factory->make($payload->provider);
        return $g->handleWebhook($payload);
    }
}
