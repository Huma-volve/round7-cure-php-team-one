<?php

namespace Database\Factories;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'provider' => $this->faker->randomElement(['card', 'apple_pay', 'paypal']),
            'brand' => $this->faker->randomElement(['visa', 'mastercard', 'amex', null]),
            'last4' => $this->faker->numerify('####'),
            'exp_month' => $this->faker->numberBetween(1, 12),
            'exp_year' => now()->year + $this->faker->numberBetween(1, 10),
            'gateway' => $this->faker->randomElement(['mock', 'stripe', 'tap']),
            'token' => $this->faker->uuid(),
            'is_default' => false,
            'metadata' => [],
        ];
    }

    public function asDefault(): self
    {
        return $this->state(fn () => ['is_default' => true]);
    }
}

