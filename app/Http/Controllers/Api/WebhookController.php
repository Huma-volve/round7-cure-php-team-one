<?php

namespace App\Http\Controllers\Api;

use App\DTOs\Payment\WebhookPayloadDTO;
use App\Http\Controllers\Controller;
use App\Services\Payment\PaymentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private readonly PaymentService $payments) {}

    public function stripe(Request $request): JsonResponse
    {
        $payload = new WebhookPayloadDTO(
            provider: 'stripe',
            headers: $request->headers->all(),
            body: $request->getContent(),
            signature: $request->header('Stripe-Signature')
        );
        $resp = $this->payments->webhook($payload);
        return $this->successResponse(['provider' => 'stripe', 'status' => $resp->getStatus()], 'webhook processed');
    }

    public function paypal(Request $request): JsonResponse
    {
        $payload = new WebhookPayloadDTO(
            provider: 'paypal',
            headers: $request->headers->all(),
            body: $request->getContent(),
            signature: $request->header('PayPal-Transmission-Sig')
        );
        $resp = $this->payments->webhook($payload);
        return $this->successResponse(['provider' => 'paypal', 'status' => $resp->getStatus()], 'webhook processed');
    }
}
