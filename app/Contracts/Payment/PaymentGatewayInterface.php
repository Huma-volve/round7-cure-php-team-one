<?php

namespace App\Contracts\Payment;

use App\DTOs\Payment\CreatePaymentDTO;
use App\DTOs\Payment\PaymentResponseDTO;
use App\DTOs\Payment\WebhookPayloadDTO;

interface PaymentGatewayInterface
{
    /**
     * Create a payment intent/order and return client data for frontend (client_secret or approve_url).
     */
    public function createPayment(CreatePaymentDTO $payload): PaymentResponseDTO;

    /**
     * Capture/confirm a pending payment if applicable.
     */
    public function confirm(string $paymentId, array $context = []): PaymentResponseDTO;

    /**
     * Verify and process webhook notifications from the provider.
     */
    public function handleWebhook(WebhookPayloadDTO $payload): PaymentResponseDTO;
}
