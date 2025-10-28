<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $users = [
            [
                'name' => 'Eslam',
                'email' => 'eslam@example.com',
                'mobile' => '01000000001',
                'password' => Hash::make('password'),
                'birthdate' => '1998-05-01',
                'profile_photo' => null,
                'location_lat' => 30.05000000,
                'location_lng' => 31.23333333,
            ],
            [
                'name' => 'Ahmed',
                'email' => 'ahmed@example.com',
                'mobile' => '01000000002',
                'password' => Hash::make('password'),
                'birthdate' => '1995-10-10',
                'profile_photo' => null,
                'location_lat' => 30.01234567,
                'location_lng' => 31.20000000,
            ],
            [
                'name' => 'Sara',
                'email' => 'sara@example.com',
                'mobile' => '01000000003',
                'password' => Hash::make('password'),
                'birthdate' => '2000-01-20',
                'profile_photo' => null,
                'location_lat' => 29.96298100,
                'location_lng' => 31.26154300,
            ],
        ];

        foreach ($users as $userData) {
           User::create($userData);
        }
    }
}
