<?php

namespace Database\Seeders;

use App\Models\Booking;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = \App\Models\Doctor::all();
        $patients = \App\Models\Patient::all();

        if ($doctors->count() == 0 || $patients->count() == 0) {
            $this->command->warn('No doctors or patients found. Seed them first.');
            return;
        }

        Booking::factory()
        ->count(20)
        ->create([
            'doctor_id' => fn () => $doctors->random()->id,
            'patient_id' => fn () => $patients->random()->id,
        ]);
    }
}
