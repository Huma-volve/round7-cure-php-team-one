<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class ContactInfoSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'app_name' => 'Cure',
            'description' => 'Cure helps you find trusted doctors, book appointments, and manage your health quickly and easily.',
            'footer_tagline' => 'Cure helps you find trusted doctors, book appointments, and manage your health—quickly and easily.',
            'footer_phone' => '080 707 555 321',
            'footer_email' => 'demo@example.com',
            'footer_address' => '526 Melrose Street, Water Mill, 11976 New York',
            'social_facebook' => 'https://facebook.com/curehealth',
            'social_whatsapp' => 'https://wa.me/2001112345678',
            'social_youtube' => 'https://youtube.com/@curehealth',
            'social_linkedin' => 'https://linkedin.com/company/curehealth',
        ];

        foreach ($defaults as $key => $value) {
            Setting::setValue($key, $value);
        }
    }
}

