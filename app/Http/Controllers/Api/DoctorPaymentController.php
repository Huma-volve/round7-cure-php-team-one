<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentResource;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Repositories\PaymentRepository;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorPaymentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private PaymentRepository $paymentRepository,
        private BookingRepository $bookingRepository
    ) {}

    /**
     * عرض جميع المدفوعات للطبيب
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $doctor = $this->getAuthenticatedDoctor();

            $payments = $this->paymentRepository->getDoctorPayments($doctor->id, [
                'status' => $request->status,
                'date_from' => $request->date_from,
                'date_to' => $request->date_to,
            ]);

            return $this->paginatedResponse(
                PaymentResource::collection($payments)->response()->getData(true),
                'تم جلب المدفوعات بنجاح'
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * عرض مدفوعة حجز محدد
     */
    public function getBookingPayment($bookingId): JsonResponse
    {
        try {
            $doctor = $this->getAuthenticatedDoctor();

            $booking = $this->bookingRepository->findByIdWithRelations($bookingId);

            if (!$booking) {
                return $this->notFoundResponse('الموعد غير موجود');
            }

            if (!$this->canAccessBooking($booking, $doctor)) {
                return $this->unauthorizedResponse('هذا الحجز ليس لك');
            }

            $payment = $this->paymentRepository->getBookingPayment($bookingId);

            if (!$payment) {
                return $this->notFoundResponse('لا توجد مدفوعة لهذا الحجز');
            }

            return $this->successResponse(
                new PaymentResource($payment),
                'تم جلب بيانات المدفوعة بنجاح'
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * إحصائيات المدفوعات للطبيب
     */
    public function getStats(): JsonResponse
    {
        try {
            $doctor = $this->getAuthenticatedDoctor();

            $stats = $this->paymentRepository->getDoctorPaymentStats($doctor->id);

            return $this->successResponse(
                $stats,
                'تم جلب إحصائيات المدفوعات بنجاح'
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * الحصول على بيانات الطبيب المصادق عليه
     */
    private function getAuthenticatedDoctor()
    {
        $doctor = Auth::user()->doctor;

        if (!$doctor) {
            throw new \Exception('لم يتم العثور على بيانات الطبيب', 404);
        }

        return $doctor;
    }

    /**
     * التحقق من صلاحية الوصول للـ booking
     */
    private function canAccessBooking(Booking $booking, $doctor): bool
    {
        return $booking->doctor_id === $doctor->id || Auth::user()->hasRole('admin', 'api');
    }

    /**
     * معالجة الأخطاء
     */
    private function handleException(\Exception $e): JsonResponse
    {
        $statusCode = ($e->getCode() >= 400 && $e->getCode() < 600)
            ? $e->getCode()
            : 500;

        return match($statusCode) {
            409 => $this->conflictResponse($e->getMessage()),
            404 => $this->notFoundResponse($e->getMessage()),
            403 => $this->unauthorizedResponse($e->getMessage()),
            default => $this->serverErrorResponse('حدث خطأ أثناء العملية', $e->getMessage())
        };
    }
}

