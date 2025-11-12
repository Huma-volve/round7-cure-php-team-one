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
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Comment\Doc;

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
    public function index()

    {
        $doctors = $this->doctorService->getAllDoctors();

        if(!$doctors){
            return response()->json([
               'status' => false ,
               'message' => 'Not Found Doctors '
            ],404);
        }

     return response()->json([
        
    'status' => true,
    'message' => 'The list of doctors was successfully retrieved.',
    'data' => DoctorResource::collection($doctors)->response()->getData(true)['data'],
    'meta' => DoctorResource::collection($doctors)->response()->getData(true)['meta'] ?? null,
    'links' => DoctorResource::collection($doctors)->response()->getData(true)['links'] ?? null,
    ]);

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
     * 
     * Query Parameters:
     * - days: عدد الأيام القادمة (افتراضي: 14 يوم، الحد الأقصى: 90 يوم)
     */
    public function getAvailableSlots($doctorId, Request $request): JsonResponse
    {
        try {
            $doctor = Doctor::findOrFail($doctorId);

            // الحصول على عدد الأيام من query parameter (افتراضي: 14 يوم)
            $daysAhead = (int) $request->input('days', 14);
            
            // تحديد الحد الأقصى (90 يوم) والحد الأدنى (1 يوم)
            $daysAhead = max(1, min(90, $daysAhead));

            $slots = $this->bookingService->generateAvailableSlots($doctor, $daysAhead);
            $availabilityWithDates = $this->formatAvailabilityWithDates($doctor, $daysAhead);

            // تجميع المواعيد حسب التاريخ لتقليل حجم الـ response
            $groupedSlots = $this->groupSlotsByDate($slots);

            // إنشاء doctor data بدون availability لأننا نعيده بشكل منفصل
            $doctorData = (new DoctorResource($doctor->load('user')))->toArray(request());
            unset($doctorData['availability']);

            return $this->successResponse([
                'doctor' => $doctorData,
                'available_slots' => $groupedSlots,
                'availability' => $availabilityWithDates,
                'period' => [
                    'days' => $daysAhead,
                    'from' => Carbon::now()->format('Y-m-d'),
                    'to' => Carbon::now()->addDays($daysAhead - 1)->format('Y-m-d'),
                ],
            ], 'تم جلب الأوقات المتاحة بنجاح');
        } catch (\Exception $e) {
            return $this->handleException($e);
        }
    }

    /**
     * تجميع المواعيد حسب التاريخ مع إمكانية الوصول للتاريخ واليوم بسهولة
     * 
     * @param array $slots
     * @return array
     */
    private function groupSlotsByDate(array $slots): array
    {
        $grouped = [];
        
        foreach ($slots as $slot) {
            $dateTime = Carbon::parse($slot['datetime']);
            $date = $dateTime->format('Y-m-d');
            $dayName = $dateTime->format('l'); // Monday, Tuesday, etc.
            $time = $dateTime->format('H:i');
            
            if (!isset($grouped[$date])) {
                $grouped[$date] = [
                    'day_name' => $dayName,
                    'times' => []
                ];
            }
            
            // إضافة الوقت إلى array الأوقات
            $grouped[$date]['times'][] = $time;
        }
        
        // ترتيب الأوقات في كل تاريخ
        foreach ($grouped as $date => $data) {
            sort($grouped[$date]['times']);
        }
        
        return $grouped;
    }

    /**
     * تحويل availability من أيام الأسبوع إلى تواريخ فعلية
     * 
     * @param Doctor $doctor
     * @param int $daysAhead عدد الأيام القادمة (الافتراضي: 14 يوم)
     * @return array
     */
    private function formatAvailabilityWithDates(Doctor $doctor, int $daysAhead = 14): array
    {
        $availability = $doctor->availability_json;
        
        // إذا لم يحدد الطبيب توفراته، ارجع مصفوفة فارغة
        if (!$availability || empty($availability)) {
            return [];
        }
        
        // تحويل البنية القديمة (day, from, to) إلى البنية الجديدة إذا لزم الأمر
        $availability = $this->normalizeAvailabilityStructure($availability);
        
        $today = Carbon::now();
        $availabilityWithDates = [];
        
        // المرور على الأيام القادمة
        for ($i = 0; $i < $daysAhead; $i++) {
            $date = $today->copy()->addDays($i);
            $dayName = strtolower($date->format('l')); // sunday, monday, etc.
            $dayNameFormatted = $date->format('l'); // Monday, Tuesday, etc.
            
            // التحقق من وجود اليوم في جدول التوفر
            if (isset($availability[$dayName]) && !empty($availability[$dayName])) {
                $dateString = $date->format('Y-m-d');
                // إضافة structure يسمح بالوصول للتاريخ واليوم بسهولة
                $availabilityWithDates[$dateString] = [
                    'day_name' => $dayNameFormatted,
                    'times' => $availability[$dayName]
                ];
            }
        }
        
        return $availabilityWithDates;
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
