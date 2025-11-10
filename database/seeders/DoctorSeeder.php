<?php

namespace Database\Seeders;

use App\Models\Doctor;
use App\Models\Specialty;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

     $doctorRole = Role::firstOrCreate(['name' => 'doctor']);



       User::factory()
            ->count(10)
            ->create()
            ->each(function ($user) use ($doctorRole) {
                $user->assignRole($doctorRole);

                Doctor::factory()->create([
                    'user_id' => $user->id,
                ]);
            });


    }
}
