<?php

namespace App\Services\Payment;

use App\Services\Payment\Gateways\CashGateway;
use App\Services\Payment\Gateways\PayPalGateway;
use App\Services\Payment\Gateways\StripeGateway;
use Stripe\StripeClient;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaymentFactory
{
    public function make(string $method): object
    {
        return match (strtolower($method)) {
            'stripe' => new StripeGateway(app(StripeClient::class)),
            'paypal' => new PayPalGateway(app(PayPalClient::class)),
            default => new CashGateway(),
        };
    }
}
