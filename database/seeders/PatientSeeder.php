<?php

namespace Database\Seeders;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PatientSeeder extends Seeder
{

    public function run(): void
    {
            $patientRole = Role::firstOrCreate(['name' => 'patient']);

            User::factory()
            ->count(10)
            ->create()
            ->each(function ($user) use ($patientRole) {
                $user->assignRole($patientRole);
                
                Patient::factory()->create([
                    'user_id' => $user->id,
                ]);
            });



    }
}
