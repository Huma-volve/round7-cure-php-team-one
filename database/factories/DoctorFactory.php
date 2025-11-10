<?php

namespace Database\Factories;

use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
          $specialties = Specialty::pluck('id')->toArray();
        return [

            'user_id' => User::factory(), // أو ممكن تختار random ID لو فيه داتا قديمة
            'specialty_id' => $this->faker->randomElement($specialties),
            'license_number' => strtoupper($this->faker->bothify('LIC-####')),
            'clinic_address' => $this->faker->address(),
            'consultation' => $this->faker->randomElement(['home', 'clinic']),
            'latitude' => $this->faker->latitude(29, 31),   // مثال لمصر
            'longitude' => $this->faker->longitude(30, 33),
            'session_price' => $this->faker->randomFloat(2, 100, 500),
            'availability_json' => json_encode([
                'monday' => ['09:00' => '17:00'],
                'tuesday' => ['09:00' => '17:00'],
                'wednesday' => ['09:00' => '17:00'],
            ]),
            'status' => $this->faker->randomElement(['active', 'inactive' , 'suspended']),
        ];




    }
}
