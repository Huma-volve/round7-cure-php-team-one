<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::inRandomOrder()->value('id') ?? Booking::factory(),
            'patient_id' => Patient::inRandomOrder()->value('id') ?? Patient::factory(),
            'doctor_id'  => Doctor::inRandomOrder()->value('id') ?? Doctor::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'comment' => $this->faker->sentence(),
        ];
    }
}
