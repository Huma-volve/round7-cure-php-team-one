<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $this->call(UserSeeder::class);
        $this->call(SpecialtySeeder::class);
        $this->call(DoctorSeeder::class);
        $this->call(PatientSeeder::class);
        $this->call(BookingSeeder::class);
        $this->call(ReviewSeeder::class);

        // Seed Dashboard data (bookings, payments, disputes, tickets)
        $this->call(DashboardDataSeeder::class);


    }
}
