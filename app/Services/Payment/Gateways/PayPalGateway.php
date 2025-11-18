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
        
        // Check for errors first
        if (isset($captured['error']) || isset($captured['error_description']) || isset($captured['name'])) {
            return new PaymentResponseDTO(
                successful: false,
                provider: 'paypal',
                paymentId: $paymentId,
                clientSecret: null,
                approveUrl: null,
                status: 'FAILED',
                raw: $captured,
            );
        }
        
        // PayPal response structure can vary, check multiple possible locations for status
        $status = $captured['status'] 
            ?? $captured['purchase_units'][0]['payments']['captures'][0]['status'] 
            ?? 'UNKNOWN';
        
        // Check if status indicates success
        $isSuccessful = in_array(strtoupper($status), ['COMPLETED', 'APPROVED']);
        
        // Get the actual capture ID if available
        $captureId = $captured['id'] 
            ?? $captured['purchase_units'][0]['payments']['captures'][0]['id'] 
            ?? $paymentId;
        
        return new PaymentResponseDTO(
            successful: $isSuccessful,
            provider: 'paypal',
            paymentId: $captureId,
            clientSecret: null,
            approveUrl: null,
            status: (string)$status,
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
