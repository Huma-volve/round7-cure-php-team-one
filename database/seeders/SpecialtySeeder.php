<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      $specialties = [
            'Cardiology',
            'Dermatology',
            'Pediatrics',
            'Dentist',
            'Neurology',
            'Ophthalmology',
            'General Practice',
        ];
        foreach ($specialties as $specialtyName) {
           Specialty::create(['name' => $specialtyName]);
        }
    }
}
