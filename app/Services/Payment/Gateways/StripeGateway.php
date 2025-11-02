<?php

namespace App\Services\Payment\Gateways;

use App\Contracts\Payment\PaymentGatewayInterface;
use App\DTOs\Payment\CreatePaymentDTO;
use App\DTOs\Payment\PaymentResponseDTO;
use App\DTOs\Payment\WebhookPayloadDTO;
use Stripe\StripeClient;

class StripeGateway implements PaymentGatewayInterface
{
    public function __construct(private readonly StripeClient $client)
    {
    }

    public function createPayment(CreatePaymentDTO $payload): PaymentResponseDTO
    {
        $intent = $this->client->paymentIntents->create([
            'amount' => (int) round(((float)$payload->amount) * 100),
            'currency' => strtolower($payload->currency),
            'metadata' => $payload->metadata,
            'description' => $payload->description,
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return new PaymentResponseDTO(
            successful: true,
            provider: 'stripe',
            paymentId: $intent->id,
            clientSecret: $intent->client_secret,
            approveUrl: null,
            status: (string) $intent->status,
            raw: $intent->toArray(),
        );
    }

    public function confirm(string $paymentId, array $context = []): PaymentResponseDTO
    {
        $intent = $this->client->paymentIntents->retrieve($paymentId);
        return new PaymentResponseDTO(
            successful: in_array($intent->status, ['succeeded', 'processing']),
            provider: 'stripe',
            paymentId: $intent->id,
            clientSecret: $intent->client_secret,
            approveUrl: null,
            status: (string) $intent->status,
            raw: $intent->toArray(),
        );
    }

    public function handleWebhook(WebhookPayloadDTO $payload): PaymentResponseDTO
    {
        // Verification is typically done via signature & endpoint secret;
        // leave minimal echo of payload for now.
        return new PaymentResponseDTO(
            successful: true,
            provider: 'stripe',
            status: 'processing',
            raw: ['headers' => $payload->headers, 'body' => $payload->body]
        );
    }
}
