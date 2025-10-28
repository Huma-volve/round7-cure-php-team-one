<?php

namespace App\Constants;

class PaymentMethod
{
    const CASH = 'cash';
    const PAYPAL = 'paypal';
    const STRIPE = 'stripe';

    /**
     * جلب جميع طرق الدفع
     */
    public static function all(): array
    {
        return [
            self::CASH,
            self::PAYPAL,
            self::STRIPE,
        ];
    }

    /**
     * التحقق من طريقة دفع صحيحة
     */
    public static function isValid(string $method): bool
    {
        return in_array($method, self::all());
    }

    /**
     * جلب التسمية
     */
    public static function label(string $method): string
    {
        return match($method) {
            self::CASH => 'نقداً',
            self::PAYPAL => 'PayPal',
            self::STRIPE => 'Stripe',
            default => $method,
        };
    }
}

