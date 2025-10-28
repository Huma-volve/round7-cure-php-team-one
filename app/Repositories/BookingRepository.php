<?php

namespace App\Repositories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class BookingRepository
{
    /**
     * جلب مواعيد المريض
     */
    public function getPatientBookings(int $patientId, array $filters = []): LengthAwarePaginator
    {
        $query = Booking::where('patient_id', $patientId)
            ->with(['doctor.user', 'patient.user']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['upcoming_only']) && $filters['upcoming_only']) {
            $query->where('date_time', '>=', now());
        }

        return $query->orderBy('date_time', 'desc')->paginate(15);
    }

    /**
     * جلب مواعيد الطبيب
     */
    public function getDoctorBookings(int $doctorId, array $filters = []): LengthAwarePaginator
    {
        $query = Booking::where('doctor_id', $doctorId)
            ->with(['patient.user']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['upcoming_only']) && $filters['upcoming_only']) {
            $query->where('date_time', '>=', now());
        }

        return $query->orderBy('date_time', 'desc')->paginate(15);
    }

    /**
     * جلب المواعيد القادمة للطبيب
     */
    public function getDoctorUpcomingBookings(int $doctorId, int $limit = 10): Collection
    {
        return Booking::where('doctor_id', $doctorId)
            ->where('date_time', '>=', now())
            ->whereNotIn('status', [Booking::STATUS_CANCELLED])
            ->with(['patient.user'])
            ->orderBy('date_time', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * جلب المواعيد المعلقة للطبيب
     */
    public function getDoctorPendingBookings(int $doctorId, int $limit = 10): Collection
    {
        return Booking::where('doctor_id', $doctorId)
            ->where('status', Booking::STATUS_PENDING)
            ->with(['patient.user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * إحصائيات الطبيب
     */
    public function getDoctorStats(int $doctorId): array
    {
        return [
            'total_upcoming' => Booking::where('doctor_id', $doctorId)
                ->where('date_time', '>=', now())
                ->whereNotIn('status', [Booking::STATUS_CANCELLED])
                ->count(),
            'pending' => Booking::where('doctor_id', $doctorId)
                ->where('status', Booking::STATUS_PENDING)
                ->count(),
            'today' => Booking::where('doctor_id', $doctorId)
                ->whereDate('date_time', today())
                ->whereNotIn('status', [Booking::STATUS_CANCELLED])
                ->count(),
        ];
    }

    /**
     * التحقق من تعارض المواعيد
     */
    public function hasConflict(int $doctorId, string $dateTime, ?int $excludeBookingId = null): bool
    {
        $query = Booking::where('doctor_id', $doctorId)
            ->where('date_time', $dateTime)
            ->whereNotIn('status', [Booking::STATUS_CANCELLED]);

        if ($excludeBookingId) {
            $query->where('id', '!=', $excludeBookingId);
        }

        return $query->exists();
    }

    /**
     * جلب تفاصيل الموعد مع العلاقات
     */
    public function findByIdWithRelations(int $id): ?Booking
    {
        return Booking::with(['doctor.user', 'patient.user'])
            ->find($id);
    }
}

