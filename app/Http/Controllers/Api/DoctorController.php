<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\DoctorDetailsResource;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;
use App\Repositories\BookingRepository;
use App\Services\Booking\BookingService;
use App\Services\DoctorService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected DoctorService $doctorService,
        private BookingService $bookingService,
        private BookingRepository $bookingRepository
    ) {}

    /**
     * عرض قائمة الأطباء
     */
    public function index(): JsonResponse

    {
        $doctors = $this->doctorService->getAllDoctors();
        return $this->successResponse($doctors, 'تم جلب قائمة الأطباء بنجاح');
    }

    /**
     * عرض تفاصيل طبيب
     */
    public function showDoctor(Request $request, $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $doctor = $this->doctorService->getDoctorDetails($id, $user);

            return $this->successResponse(
                new DoctorDetailsResource($doctor),
                'تم جلب بيانات الطبيب بنجاح'
            );
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * لوحة تحكم الطبيب
     */
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
