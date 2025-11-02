<?php

namespace App\DTOs\Payment;

class WebhookPayloadDTO
{
    public function __construct(
        public readonly string $provider,    // stripe|paypal
        public readonly array $headers,
        public readonly string $body,
        public readonly ?string $signature = null,
    ) {}
}
