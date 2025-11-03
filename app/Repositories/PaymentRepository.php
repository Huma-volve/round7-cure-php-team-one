<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class PaymentRepository
{
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function update(Payment $payment, array $data): Payment
    {
        $payment->update($data);
        return $payment;
    }

    /**
     * جلب مدفوعات الطبيب عبر الحجوزات
     */
    public function getDoctorPayments(int $doctorId, array $filters = []): LengthAwarePaginator
    {
        $query = Payment::whereHas('booking', function ($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        })->with(['booking.patient.user']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', Carbon::parse($filters['date_from']));
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', Carbon::parse($filters['date_to']));
        }

        return $query->orderBy('created_at', 'desc')->paginate(15);
    }

    /**
     * جلب مدفوعة حجز محدد
     */
    public function getBookingPayment(int $bookingId): ?Payment
    {
        return Payment::where('booking_id', $bookingId)->first();
    }

    /**
     * إحصائيات المدفوعات للطبيب
     */
    public function getDoctorPaymentStats(int $doctorId): array
    {
        $payments = Payment::whereHas('booking', function ($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        })->get();

        return [
            'total' => $payments->count(),
            'total_amount' => (float) $payments->sum('amount'),
            'success' => $payments->where('status', 'success')->count(),
            'success_amount' => (float) $payments->where('status', 'success')->sum('amount'),
            'failed' => $payments->where('status', 'failed')->count(),
            'failed_amount' => (float) $payments->where('status', 'failed')->sum('amount'),
            'pending' => $payments->where('status', 'pending')->count(),
            'pending_amount' => (float) $payments->where('status', 'pending')->sum('amount'),
        ];
    }
}
