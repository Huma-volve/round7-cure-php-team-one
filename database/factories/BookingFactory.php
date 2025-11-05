<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    protected $model =Booking::class;
    
    public function definition(): array
    {
        return [
            'date_time' => $this->faker->dateTimeBetween('+1 days', '+1 month'),
            'payment_method' => $this->faker->randomElement(['paypal', 'stripe', 'cash']),
            'price' => $this->faker->numberBetween(50, 500),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled', 'rescheduled']),
        ];
    }
}
