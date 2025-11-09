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

                        'availability_json' => [
                'monday' => ['09:00', '10:00', '11:00', '14:00', '15:00'],
                'tuesday' => ['09:00', '10:00', '11:00', '14:00', '15:00'],
                'wednesday' => ['09:00', '10:00', '11:00', '14:00', '15:00'],
                'thursday' => ['09:00', '10:00', '11:00'],
                'friday' => [],
                'saturday' => ['10:00', '11:00'],
                'sunday' => [],
            ],
                'consultation' => 'both',

            ],
            [
                'specialty_id' => $specialties->where('name', 'Dermatology')->first()->id,
                'license_number' => 'LIC-002',
                'clinic_address' => 'Heliopolis, Cairo',
                'latitude' => 30.0480,
                'longitude' => 31.2300,
                'session_price' => 350.00,

                'availability_json' =>[
                'monday' => ['09:00', '10:00', '11:00', '14:00', '15:00'],
                'tuesday' => ['09:00', '10:00', '11:00', '14:00', '15:00'],
                'wednesday' => ['09:00', '10:00'],
                'thursday' => ['09:00', '10:00', '11:00'],
                'friday' => [],
                'saturday' => ['10:00', '11:00'],
                'sunday' => [],
            ],


                'consultation' => 'clinic',

            ],

            [
                'specialty_id' => $specialties->where('name', 'Pediatrics')->first()->id,
                'license_number' => 'LIC-003',
                'clinic_address' => 'Maadi, Alex',
                'latitude' => 31.2001,
                'longitude' => 29.9187,
                'session_price' => 300.00,
                'availability_json' => [
                'monday' => ['09:00', '10:00', '11:00', '14:00', '15:00'],
                'tuesday' => ['09:00', '10:00', '11:00', '14:00', '15:00'],
                'wednesday' => [],
                'thursday' => ['09:00', '10:00', '11:00'],
                'friday' =>['10:00', '11:00'],
                'saturday' => ['10:00', '1:00'],
                'sunday' => [],
                ],
                'consultation' => 'home',
            ],
        ] ;



        foreach ($users as $index => $user) {
            Doctor::create(array_merge(['user_id' => $user->id], $doctorsData[$index]));
        $user->assignRole('doctor');
        }



    }
}
