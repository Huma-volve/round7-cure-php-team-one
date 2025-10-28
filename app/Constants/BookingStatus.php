<?php

namespace App\Constants;

class BookingStatus
{
    const PENDING = 'pending';
    const CONFIRMED = 'confirmed';
    const CANCELLED = 'cancelled';
    const RESCHEDULED = 'rescheduled';

    /**
     * جلب جميع الحالات
     */
    public static function all(): array
    {
        return [
            self::PENDING,
            self::CONFIRMED,
            self::CANCELLED,
            self::RESCHEDULED,
        ];
    }

    /**
     * التحقق من حالة صحيحة
     */
    public static function isValid(string $status): bool
    {
        return in_array($status, self::all());
    }
}

