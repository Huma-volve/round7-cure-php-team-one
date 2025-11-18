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
            'Dentistry',
            'Neurology',
            'Ophthalmology',
            'General Practice',
            'Orthopedics',
            'Oncology',
            'Endocrinology',
            'Gastroenterology',
            'Pulmonology',
            'Nephrology',
            'Rheumatology',
            'Hematology',
            'Infectious Diseases',
            'Allergy and Immunology',
            'Psychiatry',
            'Psychology',
            'Obstetrics and Gynecology',
            'Urology',
            'Otolaryngology (ENT)',
            'Dermatologic Surgery',
            'Plastic Surgery',
            'Emergency Medicine',
            'Family Medicine',
            'Sports Medicine',
            'Pain Management',
            'Internal Medicine',
            'Geriatrics',
            'Palliative Care',
            'Radiology',
            'Nuclear Medicine',
            'Pathology',
            'Anesthesiology',
            'Critical Care',
            'Sleep Medicine',
            'Rehabilitative Medicine',
            'Clinical Nutrition',
            'Diabetology',
            'Public Health',
            'Occupational Medicine',
            'Dental Surgery',
            'Periodontics',
            'Prosthodontics',
            'Endodontics',
            'Pediatric Dentistry',
            'Oral and Maxillofacial Surgery',
            'Speech and Language Therapy',
            'Audiology',
            'Genetics and Genomics',
        ];

        foreach ($specialties as $specialtyName) {
            Specialty::firstOrCreate(['name' => $specialtyName]);
        }
    }
}
