<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\Payment\PaymentGatewayInterface;
use App\DTOs\Payment\CreatePaymentDTO;
use App\DTOs\Payment\PaymentResponseDTO;
use App\DTOs\Payment\WebhookPayloadDTO;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly PayPalClient $client)
    {
        // تهيئة كسولة اعتماداً على config/paypal.php
        $this->client->setApiCredentials(config('paypal'));
        $this->client->setAccessToken($this->client->getAccessToken());
    }

    public function createPayment(CreatePaymentDTO $payload): PaymentResponseDTO
    {
        $order = $this->client->createOrder([
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => strtoupper($payload->currency),
                    'value' => (string)$payload->amount,
                ],
                'description' => $payload->description,
            ]],
            'application_context' => [
                'return_url' => $payload->returnUrl,
                'cancel_url' => $payload->cancelUrl,
            ],
        ]);

        $approveUrl = collect($order['links'] ?? [])->firstWhere('rel', 'approve')['href'] ?? null;

        return new PaymentResponseDTO(
            successful: true,
            provider: 'paypal',
            paymentId: $order['id'] ?? null,
            clientSecret: null,
            approveUrl: $approveUrl,
            status: (string)($order['status'] ?? 'CREATED'),
            raw: $order,
        );
    }

    public function confirm(string $paymentId, array $context = []): PaymentResponseDTO
    {
        $captured = $this->client->capturePaymentOrder($paymentId);
        return new PaymentResponseDTO(
            successful: ($captured['status'] ?? '') === 'COMPLETED',
            provider: 'paypal',
            paymentId: $captured['id'] ?? $paymentId,
            clientSecret: null,
            approveUrl: null,
            status: (string)($captured['status'] ?? 'COMPLETED'),
            raw: $captured,
        );
    }

    public function handleWebhook(WebhookPayloadDTO $payload): PaymentResponseDTO
    {
        return new PaymentResponseDTO(
            successful: true,
            provider: 'paypal',
            status: 'processing',
            raw: ['headers' => $payload->headers, 'body' => $payload->body]
        );
    }
}
