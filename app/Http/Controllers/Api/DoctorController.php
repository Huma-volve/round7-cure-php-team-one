<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BookingResource;
use App\Http\Resources\DoctorDetailsResource;
use App\Http\Resources\PatientDetailsResource;
use App\Models\Booking;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Review;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\PatientResource;
use App\Models\Doctor;
use App\Repositories\BookingRepository;
use App\Services\Booking\BookingService;
use App\Services\SearchService;
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
        protected SearchService $searchService,
        private BookingService $bookingService,
        private BookingRepository $bookingRepository
    ) {}

    public function  searchPatients(Request $request)
    {
        $doctorId = $request->user()->doctor->id;
        $searchTerm = $request->input('search');
        $patients = $this->searchService->searchDoctorPatients( $doctorId, $searchTerm );

        return response()->json([
            'data' => PatientResource::collection($patients),
            'message' => 'تم جلب نتائج البحث بنجاح'
        ], 200) ;

    } // End Search Patients

    /**
     * عرض قائمة الأطباء
     */
    public function index(): JsonResponse

    {
        $doctors = $this->doctorService->getAllDoctors();

        if(!$doctors){
            return response()->json([
               'status' => false ,
               'message' => 'Not Found Doctors '
            ],404);
        }

        return response()->json(
        [
               'message' =>  'The list of doctors was successfully retrieved.',
               'data'  => DoctorResource::collection($doctors) ,

        ], 200);
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

            $upcomingBookings  = $this->bookingRepository->getDoctorUpcomingBookings($doctor->id);
            $pendingBookings   = $this->bookingRepository->getDoctorPendingBookings($doctor->id);
            $stats             = $this->bookingRepository->getDoctorStats($doctor->id);

            $totalBookings = Booking::where('doctor_id', $doctor)->count();
            $totalEarnings = Payment::whereHas('booking', fn($q) => $q->where('doctor_id', $doctor))
            ->sum('amount');
            $averageRating = Review::where('doctor_id', $doctor)->avg('rating');

            return $this->successResponse([

                'totalBookings' => $totalBookings,
                'totalEarnings' => $totalEarnings,
                'averageRating' => $averageRating,

                'upcoming' => BookingResource::collection($upcomingBookings),
                'pending' => BookingResource::collection($pendingBookings),
                'stats' => $stats,
                ''
            ], 'تم جلب بيانات لوحة التحكم بنجاح');


        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }
           /**
            * عرض تفاصيل المريض للدكتور
    */
    public function showPatient($patientId)
    {
    try{

         $patient = $this->doctorService->showPatient($patientId);

        if(!$patient){
            return  $this->notFoundResponse('لم يتم العثور على بيانات المريض');
        }

        return $this->successResponse([
            new PatientDetailsResource($patient)
        ]);

    }
    catch(\Exception $e){
    return   $this->handleException($e);
    }
}
                     // عرض ارباح الطبيب
        public function earnings()
            {

            try{

            $totalEarnings = $this->doctorService->earnings();

            return response()->json([
                'total_earnings' => $totalEarnings,
                'message' => 'تم حساب الأرباح بنجاح',
            ]);
            }catch (\Exception $e) {
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
