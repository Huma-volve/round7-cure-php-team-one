<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentDispute;
use App\Models\BookingDispute;
use App\Models\Ticket;
use App\Models\Specialty;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        // Get admin user
        $admin = User::where('email', 'admin@example.com')->first();

        // Get or create specialties
        $specialty1 = Specialty::firstOrCreate(['name' => 'Cardiology']);
        $specialty2 = Specialty::firstOrCreate(['name' => 'Pediatrics']);
        $specialty3 = Specialty::firstOrCreate(['name' => 'Dermatology']);
        $specialty4 = Specialty::firstOrCreate(['name' => 'Orthopedics']);

        // Create more doctors
        $doctors = [];
        $doctorNames = ['د. خالد أحمد', 'د. سعاد محمد', 'د. علي حسن', 'د. نورا عبدالله', 'د. يوسف محمود'];
        $doctorEmails = ['doctor3@example.com', 'doctor4@example.com', 'doctor5@example.com', 'doctor6@example.com', 'doctor7@example.com'];

        foreach ($doctorNames as $index => $name) {
            $doctorUser = User::firstOrCreate(
                ['email' => $doctorEmails[$index]],
                [
                    'name' => $name,
                    'mobile' => '055' . ($index + 5) . '000000',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            if (!$doctorUser->hasRole('doctor')) {
                $doctorUser->assignRole('doctor');
            }

            $specialty = [$specialty1, $specialty2, $specialty3, $specialty4][$index % 4];

            // تحديد consultation بشكل متنوع
            $consultationTypes = ['both', 'clinic', 'home', 'both', 'clinic'];
            
            $doctor = Doctor::firstOrCreate(
                ['user_id' => $doctorUser->id],
                [
                    'specialty_id' => $specialty->id,
                    'license_number' => 'DOC' . ($index + 100),
                    'clinic_address' => 'عنوان العيادة ' . ($index + 1),
                    'consultation' => $consultationTypes[$index % count($consultationTypes)],
                    'latitude' => 24.7136 + ($index * 0.1),
                    'longitude' => 46.6753 + ($index * 0.1),
                    'session_price' => (150 + ($index * 50)),
                    'availability_json' => [],
                ]
            );

            $doctors[] = $doctor;
        }

        // Get existing doctors
        $existingDoctors = Doctor::with('user')->get();
        $doctors = collect($doctors)->merge($existingDoctors);

        // Create more patients
        $patients = [];
        $patientNames = ['أحمد محمد', 'فاطمة علي', 'محمد خالد', 'سارة أحمد', 'علي حسن', 'نورا عبدالله', 'يوسف محمود', 'ليلى أحمد'];
        $patientEmails = ['patient3@example.com', 'patient4@example.com', 'patient5@example.com', 'patient6@example.com', 'patient7@example.com', 'patient8@example.com', 'patient9@example.com', 'patient10@example.com'];

        foreach ($patientNames as $index => $name) {
            $patientUser = User::firstOrCreate(
                ['email' => $patientEmails[$index]],
                [
                    'name' => $name,
                    'mobile' => '055' . ($index + 10) . '000000',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            if (!$patientUser->hasRole('patient')) {
                $patientUser->assignRole('patient');
            }

            $patient = Patient::firstOrCreate(
                ['user_id' => $patientUser->id],
                [
                   
                    'birthdate' => Carbon::now()->subYears(25 + $index)->subMonths($index),
                    'medical_notes' => $index % 3 == 0 ? 'ملاحظات طبية' : null,
                ]
            );

            $patients[] = $patient;
        }

        // Get existing patients
        $existingPatients = Patient::with('user')->get();
        $patients = collect($patients)->merge($existingPatients);

        // Create bookings with different statuses and dates
        $bookingStatuses = ['pending', 'confirmed', 'cancelled', 'rescheduled'];
        $paymentMethods = ['cash', 'stripe', 'paypal'];

        $bookings = [];
        for ($i = 0; $i < 50; $i++) {
            $doctor = $doctors->random();
            $patient = $patients->random();
            $status = $bookingStatuses[array_rand($bookingStatuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

            // Create bookings across different months
            $dateTime = Carbon::now()
                ->subMonths(rand(0, 6))
                ->addDays(rand(0, 30))
                ->setTime(rand(9, 17), rand(0, 59), 0);

            $booking = Booking::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'date_time' => $dateTime,
                'payment_method' => $paymentMethod,
                'status' => $status,
                'price' => $doctor->session_price,
                'created_at' => $dateTime->copy()->subDays(rand(1, 30)),
            ]);

            $bookings[] = $booking;

            // Create payment for confirmed bookings
            if ($status === 'confirmed' && rand(0, 1)) {
                $paymentStatus = ['success', 'success', 'success', 'pending', 'failed'][array_rand(['success', 'success', 'success', 'pending', 'failed'])];

                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $booking->price,
                    'transaction_id' => 'TXN' . Str::random(10),
                    'gateway' => $paymentMethod,
                    'status' => $paymentStatus,
                    'created_at' => $booking->created_at,
                ]);
            }
        }

        // Create payments for some bookings
        $confirmedBookings = Booking::where('status', 'confirmed')->get();
        foreach ($confirmedBookings->take(30) as $booking) {
            if (!$booking->payment) {
                Payment::create([
                    'booking_id' => $booking->id,
                    'amount' => $booking->price,
                    'transaction_id' => 'TXN' . Str::random(10),
                    'gateway' => $booking->payment_method,
                    'status' => 'success',
                    'created_at' => $booking->created_at,
                ]);
            }
        }

        // Create payment disputes
        $paymentDisputes = Payment::where('status', 'success')->take(8)->get();
        $openedByOptions = ['admin', 'system', 'webhook'];
        $disputeStatuses = ['open', 'under_review', 'resolved', 'rejected'];

        foreach ($paymentDisputes as $index => $payment) {
            $status = $disputeStatuses[$index % 4];

            PaymentDispute::create([
                'payment_id' => $payment->id,
                'opened_by' => $openedByOptions[array_rand($openedByOptions)],
                'reason' => 'مشكلة في الدفع رقم ' . ($index + 1),
                'status' => $status,
                'resolution_notes' => $status !== 'open' ? 'تم حل النزاع' : null,
                'created_at' => Carbon::now()->subDays(rand(1, 15)),
            ]);
        }

        // Create booking disputes
        $bookingDisputes = Booking::whereIn('status', ['confirmed', 'cancelled'])->take(7)->get();
        $disputeTypes = ['cancellation_fee', 'no_show', 'other'];
        $bookingOpenedByOptions = ['patient', 'doctor', 'admin'];
        $bookingDisputeStatuses = ['open', 'under_review', 'resolved', 'rejected'];

        foreach ($bookingDisputes as $index => $booking) {
            $status = $bookingDisputeStatuses[$index % 4];

            BookingDispute::create([
                'booking_id' => $booking->id,
                'opened_by' => $bookingOpenedByOptions[array_rand($bookingOpenedByOptions)],
                'type' => $disputeTypes[array_rand($disputeTypes)],
                'status' => $status,
                'resolution_notes' => $status !== 'open' ? 'تم حل النزاع' : null,
                'created_at' => Carbon::now()->subDays(rand(1, 20)),
            ]);
        }

        // Create support tickets
        $ticketPriorities = ['low', 'medium', 'high'];
        $ticketStatuses = ['open', 'pending', 'closed'];

        foreach ($patients->take(15) as $index => $patient) {
            Ticket::create([
                'user_id' => $patient->user_id,
                'subject' => 'مشكلة في النظام ' . ($index + 1),
                'priority' => $ticketPriorities[array_rand($ticketPriorities)],
                'status' => $ticketStatuses[array_rand($ticketStatuses)],
                'assigned_admin_id' => $admin ? $admin->id : null,
                'created_at' => Carbon::now()->subDays(rand(1, 30)),
            ]);
        }

        $this->command->info('✅ تم إنشاء بيانات Dashboard بنجاح!');
        $this->command->info('📊 تم إنشاء:');
        $this->command->info('   - ' . $doctors->count() . ' طبيب');
        $this->command->info('   - ' . $patients->count() . ' مريض');
        $this->command->info('   - ' . count($bookings) . ' حجز');
        $this->command->info('   - ' . Payment::count() . ' دفعة');
        $this->command->info('   - ' . PaymentDispute::count() . ' نزاع دفع');
        $this->command->info('   - ' . BookingDispute::count() . ' نزاع حجز');
        $this->command->info('   - ' . Ticket::count() . ' تذكرة');
    }
}
