<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Services\DoctorService;
use App\Http\Resources\BookingResource;
use App\Http\Resources\DoctorResource;
use App\Models\Booking;
use App\Models\Doctor;
use App\Repositories\BookingRepository;
use App\Services\Booking\BookingService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
     use ApiResponseTrait;

    public function __construct( protected DoctorService  $doctorService,
         private BookingService  $bookingService,
         private BookingRepository $BookingRepository )
    {
    }

    public function showDoctor($id, Request $request)
    {
        $user = Auth::user() ?? null; // ممكن تكون null لو العام لاسوء
        $doctor = $this->doctorService->getDoctorDetails($id, $user);



        return response()->json([

            'status' => true,
            'message' => 'Doctor data loaded successfully',
            'data' => $doctor
        ]);
    } // End Show


    public function dashboard(Request $request): JsonResponse
    {
        try {
            $doctor = Auth::user()->doctor;

            if (!$doctor) {
                return $this->notFoundResponse('لم يتم العثور على بيانات الطبيب');
            }

            $upcomingBookings = $this->bookingRepository->getDoctorUpcomingBookings($doctor->id);
            $pendingBookings = $this->bookingRepository->getDoctorPendingBookings($doctor->id);
            $stats = $this->bookingRepository->getDoctorStats($doctor->id);

            return $this->successResponse([
                'upcoming' => BookingResource::collection($upcomingBookings),
                'pending' => BookingResource::collection($pendingBookings),
                'stats' => $stats,
            ], 'تم جلب بيانات لوحة التحكم بنجاح');

        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * عرض جميع مواعيد الطبيب
     */
    public function bookings(Request $request): JsonResponse
    {
        try {
            $doctor = Auth::user()->doctor;

            if (!$doctor) {
                return $this->notFoundResponse('لم يتم العثور على بيانات الطبيب');
            }

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

            $doctor = Auth::user()->doctor;

            if ($booking->doctor_id != $doctor->id && !Auth::user()->hasRole('admin')) {
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
    public function confirmBooking($id): JsonResponse
    {
        try {
            $booking = Booking::findOrFail($id);
            $doctor = Auth::user()->doctor;

            if ($booking->doctor_id != $doctor->id) {
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
     * الحصول على الأوقات المتاحة
     */
    public function getAvailableSlots($doctorId): JsonResponse
    {
        try {
            $doctor = Doctor::findOrFail($doctorId);

            $slots = $this->bookingService->generateAvailableSlots($doctor);

            return $this->successResponse([
                'doctor' => new DoctorResource($doctor->load('user')),
                'available_slots' => $slots,
                'availability' => $doctor->availability_json,
            ], 'تم جلب الأوقات المتاحة بنجاح');

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








