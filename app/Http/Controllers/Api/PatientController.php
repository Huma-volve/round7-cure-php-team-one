<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BookAppointmentRequest;
use App\Http\Requests\RescheduleBookingRequest;
use App\Http\Resources\BookingResource;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Repositories\BookingRepository;
use App\Services\Booking\BookingService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        private BookingService $bookingService,
        private BookingRepository $bookingRepository
    ) {}

    /**
     * حجز موعد جديد
     */
    public function bookAppointment(BookAppointmentRequest $request): JsonResponse
    {
        try {
            $patient = Auth::user()->patient;
            
            if (!$patient) {
                return $this->notFoundResponse('لم يتم العثور على بيانات المريض');
            }

            $doctor = Doctor::findOrFail($request->doctor_id);
            
            $booking = $this->bookingService->bookAppointment($patient, $doctor, [
                'date_time' => $request->date_time,
                'payment_method' => $request->payment_method,
            ]);

            return $this->createdResponse(
                new BookingResource($booking),
                'تم حجز الموعد بنجاح'
            );

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * عرض جميع مواعيد المريض
     */
    public function myBookings(Request $request): JsonResponse
    {
        try {
            $patient = Auth::user()->patient;
            
            if (!$patient) {
                return $this->notFoundResponse('لم يتم العثور على بيانات المريض');
            }

            $bookings = $this->bookingRepository->getPatientBookings($patient->id, [
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

            $patient = Auth::user()->patient;
            
            if ($booking->patient_id != $patient->id && !Auth::user()->hasRole('admin')) {
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
     * إعادة جدولة موعد
     */
    public function reschedule(RescheduleBookingRequest $request, $id): JsonResponse
    {
        try {
            $booking = Booking::findOrFail($id);
            $patient = Auth::user()->patient;

            if ($booking->patient_id != $patient->id) {
                return $this->unauthorizedResponse('هذا الموعد ليس لك');
            }

            $booking = $this->bookingService->rescheduleBooking($booking, $request->date_time);

            return $this->successResponse(
                new BookingResource($booking),
                'تم إعادة جدولة الموعد بنجاح'
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
            $booking = Booking::findOrFail($id);
            $patient = Auth::user()->patient;

            if ($booking->patient_id != $patient->id) {
                return $this->unauthorizedResponse('هذا الموعد ليس لك');
            }

            $booking = $this->bookingService->cancelBooking($booking);

            return $this->successResponse(
                new BookingResource($booking),
                'تم إلغاء الموعد بنجاح'
            );

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * معالجة الأخطاء
     */
    private function handleException(\Exception $e): JsonResponse
    {
        $statusCode = $e->getCode() && $e->getCode() >= 400 && $e->getCode() < 600 
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

