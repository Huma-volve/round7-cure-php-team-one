<?php

namespace App\DTOs\Payment;

use App\Contracts\Payment\PaymentResponseInterface;

class PaymentResponseDTO implements PaymentResponseInterface
{
    public function __construct(
        private readonly bool $successful,
        private readonly string $provider,             // stripe|paypal|cash
        private readonly ?string $paymentId = null,
        private readonly ?string $clientSecret = null,
        private readonly ?string $approveUrl = null,
        private readonly string $status = 'pending',
        private readonly array $raw = [],
    ) {}

    public function isSuccessful(): bool { return $this->successful; }
    public function getProvider(): string { return $this->provider; }
    public function getPaymentId(): ?string { return $this->paymentId; }
    public function getClientSecret(): ?string { return $this->clientSecret; }
    public function getApproveUrl(): ?string { return $this->approveUrl; }
    public function getStatus(): string { return $this->status; }
    public function getRaw(): array { return $this->raw; }
}
