<?php

namespace Database\Seeders;


use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Booking;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        // إنشاء طبيب تجريبي
        $doctorUser = User::updateOrCreate([
            'name' => 'د. أحمد محمد',
            'email' => 'doctor@example.com',
            'mobile' => '0551111111',
            'password' => Hash::make('password'),
        ]);
        $doctorUser->assignRole('doctor');

        // إنشاء أو جلب التخصص
        $specialty1 = Specialty::firstOrCreate(['name' => 'Cardiology']);

        $doctor = Doctor::updateOrCreate([
            'user_id' => $doctorUser->id,
            'specialty_id' => $specialty1->id,
            'license_number' => 'DOC123456',
            'clinic_address' => 'الرياض، المملكة العربية السعودية',
            'consultation' => 'both',
            'latitude' => 24.7136,
            'longitude' => 46.6753,
            'session_price' => 200.00,
            'availability_json' => [
                'monday' => ['09:00', '10:00', '11:00', '14:00', '15:00'],
                'tuesday' => ['09:00', '10:00', '11:00', '14:00', '15:00'],
                'wednesday' => ['09:00', '10:00', '11:00', '14:00', '15:00'],
                'thursday' => ['09:00', '10:00', '11:00'],
                'friday' => [],
                'saturday' => ['10:00', '11:00'],
                'sunday' => [],
            ],
        ]);

        // إنشاء طبيب آخر
        $doctorUser2 = User::updateOrCreate([
            'name' => 'د. فاطمة علي',
            'email' => 'doctor2@example.com',
            'mobile' => '0552222222',
            'password' => Hash::make('password'),
        ]);
        $doctorUser2->assignRole('doctor');

        // إنشاء أو جلب التخصص
        $specialty2 = Specialty::firstOrCreate(['name' => 'Pediatrics']);

        $doctor2 = Doctor::updateOrCreate([
            'user_id' => $doctorUser2->id,
            'specialty_id' => $specialty2->id,
            'license_number' => 'DOC654321',
            'clinic_address' => 'جدة، المملكة العربية السعودية',
            'consultation' => 'clinic',
            'latitude' => 21.4858,
            'longitude' => 39.1925,
            'session_price' => 150.00,
            'availability_json' => [
                'monday' => ['09:00', '10:00', '14:00', '15:00'],
                'tuesday' => ['09:00', '10:00', '14:00', '15:00'],
                'wednesday' => ['09:00', '10:00', '14:00', '15:00'],
                'thursday' => ['10:00', '11:00'],
                'friday' => [],
                'saturday' => ['10:00', '11:00', '12:00'],
                'sunday' => [],
            ],
            'status' => 'active',
        ]);

        // إنشاء مريض تجريبي
        $patientUser = User::updateOrCreate([
            'name' => 'محمد عبدالله',
            'email' => 'patient@example.com',
            'mobile' => '0553333333',
            'password' => Hash::make('password'),
            'gender' => 'male',
        ]);
        $patientUser->assignRole('patient');

        $patient = Patient::updateOrCreate([
            'user_id' => $patientUser->id,
            'birthdate' => '1990-01-15',
            'medical_notes' => 'لا توجد ملاحظات طبية',
        ]);

        // إنشاء مريض آخر
        $patientUser2 = User::updateOrCreate([
            'name' => 'سارة أحمد',
            'email' => 'patient2@example.com',
            'mobile' => '0554444444',
            'password' => Hash::make('password'),
            'gender' => 'female',
        ]);
        $patientUser2->assignRole('patient');

        $patient2 = Patient::updateOrCreate([
            'user_id' => $patientUser2->id,
            'birthdate' => '1995-05-20',
            'medical_notes' => null,
        ]);

        // إنشاء بعض المواعيد التجريبية
        Booking::updateOrCreate([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'date_time' => now()->addDays(3)->setTime(10, 0, 0),
            'payment_method' => 'cash',
            'status' => 'pending',
            'price' => $doctor->session_price,
        ]);

        Booking::updateOrCreate([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'date_time' => now()->addDays(5)->setTime(14, 0, 0),
            'payment_method' => 'stripe',
            'status' => 'confirmed',
            'price' => $doctor->session_price,
        ]);

        Booking::updateOrCreate([
            'doctor_id' => $doctor2->id,
            'patient_id' => $patient2->id,
            'date_time' => now()->addDays(7)->setTime(10, 0, 0),
            'payment_method' => 'cash',
            'status' => 'pending',
            'price' => $doctor2->session_price,
        ]);

        // موعد في اليوم القادم
        Booking::updateOrCreate([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient2->id,
            'date_time' => now()->addDay()->setTime(9, 0, 0),
            'payment_method' => 'cash',
            'status' => 'confirmed',
            'price' => $doctor->session_price,
        ]);

        // إضافة المزيد من الحجوزات للطبيب الأول (doctor@example.com) للاختبار
        // حجز في يوم الاثنين القادم
        $nextMonday = now()->next(Carbon::MONDAY);
        Booking::updateOrCreate([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'date_time' => $nextMonday->copy()->setTime(10, 0, 0),
            'payment_method' => 'cash',
            'status' => 'confirmed',
            'price' => $doctor->session_price,
        ]);

        // حجز في يوم الثلاثاء القادم
        $nextTuesday = now()->next(Carbon::TUESDAY);
        Booking::updateOrCreate([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient2->id,
            'date_time' => $nextTuesday->copy()->setTime(14, 0, 0),
            'payment_method' => 'stripe',
            'status' => 'confirmed',
            'price' => $doctor->session_price,
        ]);

        // حجز في يوم الأربعاء القادم
        $nextWednesday = now()->next(Carbon::WEDNESDAY);
        Booking::updateOrCreate([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'date_time' => $nextWednesday->copy()->setTime(11, 0, 0),
            'payment_method' => 'cash',
            'status' => 'pending',
            'price' => $doctor->session_price,
        ]);

        // حجز في يوم الخميس القادم
        $nextThursday = now()->next(Carbon::THURSDAY);
        Booking::updateOrCreate([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient2->id,
            'date_time' => $nextThursday->copy()->setTime(9, 0, 0),
            'payment_method' => 'cash',
            'status' => 'confirmed',
            'price' => $doctor->session_price,
        ]);
    }
}

