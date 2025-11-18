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
        // Use Stripe Checkout Session instead of Payment Intent for better UX
        // User will be redirected to Stripe's official page to enter card details
        $checkoutSession = $this->client->checkout->sessions->create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($payload->currency),
                    'product_data' => [
                        'name' => $payload->description ?? 'Payment',
                    ],
                    'unit_amount' => (int) round(((float)$payload->amount) * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $payload->returnUrl ?? url('/payment-test?payment_success=true&session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => $payload->cancelUrl ?? url('/payment-test?payment_cancelled=true'),
            'metadata' => $payload->metadata,
        ]);

        return new PaymentResponseDTO(
            successful: true,
            provider: 'stripe',
            paymentId: $checkoutSession->id,
            clientSecret: null, // Not needed for Checkout
            approveUrl: $checkoutSession->url, // Redirect URL to Stripe Checkout
            status: (string) $checkoutSession->payment_status ?? 'open',
            raw: $checkoutSession->toArray(),
        );
    }

    public function confirm(string $paymentId, array $context = []): PaymentResponseDTO
    {
        // Check if it's a Checkout Session ID or Payment Intent ID
        if (str_starts_with($paymentId, 'cs_')) {
            // It's a Checkout Session
            $session = $this->client->checkout->sessions->retrieve($paymentId);
            
            // Check payment status from session
            $paymentStatus = $session->payment_status ?? 'unpaid';
            $paymentIntentId = $session->payment_intent;
            
            // If payment_intent exists, retrieve it for more details
            if ($paymentIntentId) {
                try {
                    $intent = $this->client->paymentIntents->retrieve($paymentIntentId);
                    return new PaymentResponseDTO(
                        successful: in_array($intent->status, ['succeeded', 'processing']),
                        provider: 'stripe',
                        paymentId: $intent->id,
                        clientSecret: $intent->client_secret,
                        approveUrl: null,
                        status: (string) $intent->status,
                        raw: $intent->toArray(),
                    );
                } catch (\Exception $e) {
                    // If payment intent retrieval fails, use session status
                }
            }
            
            // Return based on session payment status
            $isSuccessful = in_array($paymentStatus, ['paid', 'complete']);
            return new PaymentResponseDTO(
                successful: $isSuccessful,
                provider: 'stripe',
                paymentId: $session->id,
                clientSecret: null,
                approveUrl: null,
                status: $paymentStatus,
                raw: $session->toArray(),
            );
        }
        
        // Fallback: Try as Payment Intent ID
        try {
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
        } catch (\Exception $e) {
            // If both fail, return error response
            return new PaymentResponseDTO(
                successful: false,
                provider: 'stripe',
                paymentId: $paymentId,
                clientSecret: null,
                approveUrl: null,
                status: 'error',
                raw: ['error' => $e->getMessage()],
            );
        }
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
