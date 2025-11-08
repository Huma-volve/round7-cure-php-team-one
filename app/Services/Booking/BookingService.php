<?php

namespace App\Services\Booking;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingService
{
    /**
     * حجز موعد جديد
     */
    public function bookAppointment(Patient $patient, Doctor $doctor, array $data): Booking
    {

        // التحقق من توفر الطبيب
        $this->checkDoctorAvailability($doctor, $data['date_time']);

        // التحقق من تعارض المواعيد
        $this->checkConflictingBooking($doctor, $data['date_time']);
        
        DB::beginTransaction();

        try {
            $booking = Booking::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'date_time' => $data['date_time'],
                'payment_method' => $data['payment_method'],
                'status' => Booking::STATUS_PENDING,
                'price' => $doctor->session_price,
            ]);

            DB::commit();

            return $booking->load(['doctor.user', 'patient.user']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * التحقق من تعارض المواعيد
     */
    public function checkConflictingBooking(Doctor $doctor, string $dateTime): void
    {
        $conflictingBooking = Booking::where('doctor_id', $doctor->id)
            ->where('date_time', $dateTime)
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->first();
        
        if ($conflictingBooking) {
            throw new \Exception(__('messages.booking.conflict'), 409);
        }
    }

    /**
     * التحقق من توفر الطبيب
     */
    public function checkDoctorAvailability(Doctor $doctor, string $dateTime): void
    {
        $availability = $doctor->availability_json;
        
        if (!$availability || empty($availability)) {
            return; // إذا لم يحدد توفر، نقبل أي موعد
        }

        $slotDateTime = Carbon::parse($dateTime);
        $dayOfWeek = strtolower($slotDateTime->format('l'));
        $time = $slotDateTime->format('H:i');

        if (isset($availability[$dayOfWeek])) {
            if (!in_array($time, $availability[$dayOfWeek])) {
                throw new \Exception(__('messages.booking.unavailable'), 409);
            }
        } else {
            throw new \Exception(__('messages.booking.unavailable'), 409);
        }
    }

    /**
     * إعادة جدولة موعد
     */
    public function rescheduleBooking(Booking $booking, string $newDateTime): Booking
    {
        if (!$booking->isReschedulable()) {
            throw new \Exception(__('messages.booking.unavailable'), 400);
        }

        // التحقق من تعارض
        $conflictingBooking = Booking::where('doctor_id', $booking->doctor_id)
            ->where('date_time', $newDateTime)
            ->where('id', '!=', $booking->id)
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->first();
        
        if ($conflictingBooking) {
            throw new \Exception(__('messages.booking.conflict'), 409);
        }

        DB::beginTransaction();

        try {
            $booking->update([
                'date_time' => $newDateTime,
                'status' => Booking::STATUS_RESCHEDULED,
            ]);

            DB::commit();

            return $booking->load(['doctor.user', 'patient.user']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * إلغاء موعد
     */
    public function cancelBooking(Booking $booking): Booking
    {
        if (!$booking->isCancellable() && $booking->status !== Booking::STATUS_PENDING) {
            throw new \Exception(__('messages.booking.unavailable'), 400);
        }

        DB::beginTransaction();

        try {
            $booking->update(['status' => Booking::STATUS_CANCELLED]);

            if ($booking->payment) {
                $booking->payment->update(['status' => 'failed']);
            }

            DB::commit();

            return $booking->load(['doctor.user', 'patient.user']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * تأكيد موعد
     */
    public function confirmBooking(Booking $booking): Booking
    {
        DB::beginTransaction();

        try {
            $booking->update(['status' => Booking::STATUS_CONFIRMED]);

            DB::commit();

            return $booking->load(['doctor.user', 'patient.user']);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * توليد المواعيد المتاحة للطبيب بناءً على الجدول الموجود في الطبيب (availability_json)
     * 
     * @param Doctor $doctor الطبيب المطلوب توليد المواعيد له
     * @param int $daysAhead عدد الأيام القادمة المطلوب توليد المواعيد لها (الافتراضي: 14 يوم)
     * @return array مصفوفة بالمواعيد المتاحة بالشكل: ['datetime', 'formatted', 'day_name', 'doctor_id']
     */
    public function generateAvailableSlots(Doctor $doctor, int $daysAhead = 14): array
    {
        $slots = [];
        $availability = $doctor->availability_json;
        
        // إذا لم يحدد الطبيب توفراته، ارجع مصفوفة فارغة
        if (!$availability || empty($availability)) {
            return [];
        }
        
        $today = Carbon::now();
        
        // جلب جميع الحجوزات للطبيب في المدى الزمني المحدد (استعلام واحد فقط)
        $startDate = $today->copy()->startOfDay();
        $endDate = $today->copy()->addDays($daysAhead)->endOfDay();
        
        $bookings = Booking::where('doctor_id', $doctor->id)
            ->whereBetween('date_time', [$startDate, $endDate])
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->pluck('date_time')
            ->map(fn($dt) => Carbon::parse($dt)->format('Y-m-d H:i:s'))
            ->toArray();
        
        // المرور على الأيام القادمة
        for ($i = 0; $i < $daysAhead; $i++) {
            $date = $today->copy()->addDays($i);
            $dayName = strtolower($date->format('l')); // sunday, monday, etc.
            
            // التجاوز إذا اليوم غير موجود في جدول التوفر
            if (!isset($availability[$dayName]) || empty($availability[$dayName])) {
                continue;
            }
            
            // المرور على الأوقات المتاحة لهذا اليوم
            foreach ($availability[$dayName] as $time) {
                [$hour, $minute] = explode(':', $time);
                
                $slotDateTime = $date->copy()->setTime((int)$hour, (int)$minute, 0);
                
                // تجاهل الأوقات الماضية
                if ($slotDateTime->isPast()) {
                    continue;
                }
                
                // التحقق من عدم وجود حجز في هذا الوقت
                $datetimeString = $slotDateTime->format('Y-m-d H:i:s');
                
                if (!in_array($datetimeString, $bookings)) {
                    $slots[] = [
                        'datetime' => $datetimeString,
                        'formatted' => $slotDateTime->format('d M Y h:i A'),
                        'day_name' => $date->format('l'),
                        'doctor_id' => $doctor->id
                    ];
                }
            }
        }
        
        return $slots;
    }
}

