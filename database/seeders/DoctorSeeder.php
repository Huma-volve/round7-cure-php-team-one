<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

 $users = User::where('id', '!=', 1)->take(3)->get();
 $specialties = Specialty::all();


        $doctorsData = [
            [
                'specialty_id' => $specialties->where('name', 'Cardiology')->first()->id,
                'license_number' => 'LIC-001',
                'clinic_address' => 'Nasr City, Cairo',
                'latitude' => 30.0520,
                'longitude' => 31.2370,
                'session_price' => 400.00,
                'availability_json' => json_encode(['sat' => '5-9', 'mon' => '3-8']),
            ],
            [
                'specialty_id' => $specialties->where('name', 'Dermatology')->first()->id,
                'license_number' => 'LIC-002',
                'clinic_address' => 'Heliopolis, Cairo',
                'latitude' => 30.0480,
                'longitude' => 31.2300,
                'session_price' => 350.00,
                'availability_json' => json_encode(['sun' => '4-9', 'wed' => '3-7']),
            ],
            [
                'specialty_id' => $specialties->where('name', 'Pediatrics')->first()->id,
                'license_number' => 'LIC-003',
                'clinic_address' => 'Maadi, Alex',
                'latitude' => 31.2001,
                'longitude' => 29.9187,
                'session_price' => 300.00,
                'availability_json' => json_encode(['tue' => '2-6', 'thu' => '4-9']),
            ],
        ];
        foreach ($users as $index => $user) {
            Doctor::create(array_merge(['user_id' => $user->id], $doctorsData[$index]));
        }


    }
}
