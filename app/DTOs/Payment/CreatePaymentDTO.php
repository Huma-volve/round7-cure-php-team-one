<?php

namespace App\DTOs\Payment;

class CreatePaymentDTO
{
    public function __construct(
        public readonly int $bookingId,
        public readonly string $gateway,         // stripe|paypal|cash
        public readonly string $currency,        // e.g. SAR, USD
        public readonly string $amount,          // decimal string
        public readonly ?string $description = null,
        public readonly ?int $patientId = null,
        public readonly array $metadata = [],
        public readonly ?string $returnUrl = null,
        public readonly ?string $cancelUrl = null,
    ) {}
}
