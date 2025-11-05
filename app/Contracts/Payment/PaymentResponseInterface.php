<?php

namespace App\Contracts\Payment;

interface PaymentResponseInterface
{
    public function isSuccessful(): bool;
    public function getProvider(): string;
    public function getPaymentId(): ?string;
    public function getClientSecret(): ?string; // Stripe
    public function getApproveUrl(): ?string;   // PayPal
    public function getStatus(): string;        // pending|processing|success|failed
    public function getRaw(): array;            // raw provider payload
}
