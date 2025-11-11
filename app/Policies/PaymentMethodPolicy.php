<?php

namespace App\Policies;

use App\Models\PaymentMethod;
use App\Models\User;

class PaymentMethodPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    public function view(User $user, PaymentMethod $paymentMethod): bool
    {
        return $paymentMethod->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('patient');
    }

    public function update(User $user, PaymentMethod $paymentMethod): bool
    {
        return $paymentMethod->user_id === $user->id;
    }

    public function delete(User $user, PaymentMethod $paymentMethod): bool
    {
        return $paymentMethod->user_id === $user->id;
    }

    public function restore(User $user, PaymentMethod $paymentMethod): bool
    {
        return $paymentMethod->user_id === $user->id;
    }
}

