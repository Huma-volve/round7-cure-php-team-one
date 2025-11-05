<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Requests\RescheduleBookingRequest;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Services\Booking\BookingService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorBookingController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private BookingService $bookingService,
        private BookingRepository $bookingRepository
    ) {}

    /**
     * عرض جميع مواعيد الطبيب
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $doctor = $this->getAuthenticatedDoctor();

            $bookings = $this->bookingRepository->getDoctorBookings($doctor->id, [
                'status' => $request->status,
                'upcoming_only' => $request->boolean('upcoming_only'),
            ]);

            return $this->paginatedResponse(
                BookingResource::collection($bookings)->response()->getData(true),
                'تم جلب المواعيد بنجاح'
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * عرض تفاصيل موعد
     */
    public function show($id): JsonResponse
    {
        try {
            $booking = $this->bookingRepository->findByIdWithRelations($id);

            if (!$booking) {
                return $this->notFoundResponse('الموعد غير موجود');
            }

            $doctor = $this->getAuthenticatedDoctor();

            if (!$this->canAccessBooking($booking, $doctor)) {
                return $this->unauthorizedResponse('غير مصرح لك بعرض هذا الموعد');
            }

            return $this->successResponse(
                new BookingResource($booking),
                'تم جلب تفاصيل الموعد بنجاح'
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * تأكيد موعد
     */
    public function confirm($id): JsonResponse
    {
        try {
            $booking = $this->bookingRepository->findByIdWithRelations($id);

            if (!$booking) {
                return $this->notFoundResponse('الموعد غير موجود');
            }

            $doctor = $this->getAuthenticatedDoctor();

            if (!$this->canAccessBooking($booking, $doctor)) {
                return $this->unauthorizedResponse('هذا الموعد ليس لك');
            }

            $booking = $this->bookingService->confirmBooking($booking);

            return $this->successResponse(
                new BookingResource($booking),
                'تم تأكيد الموعد بنجاح'
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * إلغاء موعد
     */
    public function cancel($id): JsonResponse
    {
        try {
            $booking = $this->bookingRepository->findByIdWithRelations($id);

            if (!$booking) {
                return $this->notFoundResponse('الموعد غير موجود');
            }

            $doctor = $this->getAuthenticatedDoctor();

            if (!$this->canAccessBooking($booking, $doctor)) {
                return $this->unauthorizedResponse('هذا الموعد ليس لك');
            }

            $booking = $this->bookingService->cancelBooking($booking);

            return $this->successResponse(
                new BookingResource($booking->load(['doctor.user', 'patient.user'])),
                'تم إلغاء الموعد بنجاح'
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * إعادة جدولة موعد
     */
    public function reschedule(RescheduleBookingRequest $request, $id): JsonResponse
    {
        try {
            $booking = $this->bookingRepository->findByIdWithRelations($id);

            if (!$booking) {
                return $this->notFoundResponse('الموعد غير موجود');
            }

            $doctor = $this->getAuthenticatedDoctor();

            if (!$this->canAccessBooking($booking, $doctor)) {
                return $this->unauthorizedResponse('هذا الموعد ليس لك');
            }

            $booking = $this->bookingService->rescheduleBooking($booking, $request->date_time);

            return $this->successResponse(
                new BookingResource($booking->load(['doctor.user', 'patient.user'])),
                'تم إعادة جدولة الموعد بنجاح'
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
        return $booking->doctor_id === $doctor->id || Auth::user()->hasRole('admin');
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

