<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Patient>
 */
class PatientFactory extends Factory
{
    protected $model =Patient::class;
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'birthdate' => $this->faker->date(),
            'medical_notes' => $this->faker->paragraph(),
        ];
    }
}
