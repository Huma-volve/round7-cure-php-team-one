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

        // تحويل البنية القديمة إلى البنية الجديدة (array من الأوقات)
        $availability = $this->normalizeAvailabilityStructure($availability);

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
        
        // تحويل البنية القديمة إلى البنية الجديدة (array من الأوقات)
        $availability = $this->normalizeAvailabilityStructure($availability);
        
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

    /**
     * تحويل البنية القديمة للـ availability إلى البنية الجديدة
     * 
     * @param mixed $availability
     * @return array
     */
    private function normalizeAvailabilityStructure($availability): array
    {
        // إذا كانت البيانات null أو فارغة
        if (empty($availability)) {
            return [];
        }
        
        // إذا كانت البيانات string (JSON)، قم بتحويلها إلى array
        if (is_string($availability)) {
            $decoded = json_decode($availability, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $availability = $decoded;
            } else {
                return [];
            }
        }
        
        // التأكد من أن البيانات array
        if (!is_array($availability)) {
            return [];
        }
        
        // إذا كانت البيانات object واحد به day, from, to (البنية القديمة)
        if (isset($availability['day']) && isset($availability['from']) && isset($availability['to'])) {
            $dayMap = [
                'mon' => 'monday',
                'tue' => 'tuesday',
                'wed' => 'wednesday',
                'thu' => 'thursday',
                'fri' => 'friday',
                'sat' => 'saturday',
                'sun' => 'sunday',
            ];
            
            $dayName = $dayMap[strtolower($availability['day'])] ?? strtolower($availability['day']);
            
            // توليد الأوقات من from إلى to
            $from = Carbon::parse($availability['from']);
            $to = Carbon::parse($availability['to']);
            $times = [];
            
            while ($from->lte($to)) {
                $times[] = $from->format('H:i');
                $from->addHour();
            }
            
            return [$dayName => $times];
        }
        
        // إذا كانت البيانات array من objects (البنية القديمة)
        if (isset($availability[0]) && is_array($availability[0]) && isset($availability[0]['day'])) {
            $dayMap = [
                'mon' => 'monday',
                'tue' => 'tuesday',
                'wed' => 'wednesday',
                'thu' => 'thursday',
                'fri' => 'friday',
                'sat' => 'saturday',
                'sun' => 'sunday',
            ];
            
            $normalized = [];
            
            foreach ($availability as $item) {
                if (is_array($item) && isset($item['day']) && isset($item['from']) && isset($item['to'])) {
                    $dayName = $dayMap[strtolower($item['day'])] ?? strtolower($item['day']);
                    
                    // توليد الأوقات من from إلى to
                    $from = Carbon::parse($item['from']);
                    $to = Carbon::parse($item['to']);
                    $times = [];
                    
                    while ($from->lte($to)) {
                        $times[] = $from->format('H:i');
                        $from->addHour();
                    }
                    
                    if (!isset($normalized[$dayName])) {
                        $normalized[$dayName] = [];
                    }
                    
                    $normalized[$dayName] = array_unique(array_merge($normalized[$dayName], $times));
                }
            }
            
            return $normalized;
        }
        
        // إذا كانت البيانات بالبنية الجديدة (monday, tuesday, etc.)
        // لكن القيم objects بدلاً من arrays (مثل {"monday": {"09:00": "17:00"}})
        $normalized = [];
        foreach ($availability as $dayName => $times) {
            // إذا كانت القيمة object (مثل {"09:00": "17:00"})
            // حيث المفتاح هو وقت البداية والقيمة هي وقت النهاية
            if (is_array($times) && !isset($times[0]) && !empty($times)) {
                // تحويل من {"09:00": "17:00"} إلى ["09:00", "10:00", ..., "17:00"]
                $keys = array_keys($times);
                if (count($keys) >= 1) {
                    $fromTime = $keys[0]; // وقت البداية (المفتاح)
                    $toTime = $times[$keys[0]]; // وقت النهاية (القيمة)
                    
                    $from = Carbon::parse($fromTime);
                    $to = Carbon::parse($toTime);
                    $timesArray = [];
                    
                    while ($from->lte($to)) {
                        $timesArray[] = $from->format('H:i');
                        $from->addHour();
                    }
                    
                    $normalized[$dayName] = $timesArray;
                } else {
                    $normalized[$dayName] = [];
                }
            } 
            // إذا كانت القيمة array بالفعل (مثل ["09:00", "10:00"])
            elseif (is_array($times)) {
                $normalized[$dayName] = $times;
            }
        }
        
        return $normalized;
    }
}

