<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\Specialty;
use App\Services\FavoriteService;
use App\Models\User;
use App\Services\DoctorService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;


class HomeController extends Controller
{
  use ApiResponseTrait ;

   protected $doctorService;
   protected $favoriteService;

    public function __construct(DoctorService $doctorService , FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
        $this->doctorService = $doctorService;
    }

    public function index(Request $request)
    {

  try{

    $user = Auth::user();

    $lat = $request->latitude  ??    $user->location_lat ?? 30.0444     ; // Cairo center
    $lng = $request->longitude ??   $user->location_lng ?? 31.2357  ;

    $specialties = Specialty::select('id' , 'name')->get();
    $doctors = $this->doctorService->searchDoctorsNearby($user, $lat, $lng, $request->input('search'));

    $doctors->each(function($doctor) use ($user) {
        $doctor->is_favorite = $this->favoriteService->isFavorite($user, $doctor);
    });

    $formattedDoctors  = $doctors->map(function ($doctor) {
        return [
                'id'             => $doctor->id,
                'name'           => $doctor->user->name,
                'specialty'      => $doctor->specialty->name,
                'clinic_address' => $doctor->clinic_address,
                'average_rating' => $doctor->average_rating ?? 0,
                'reviews_count'  => $doctor->reviews_count ?? 0,
                'availability'   => $doctor->availability_json,
                'consultation'   => $doctor->consultation ?? 'clinic',
                'is_favorite'    => $doctor->is_favorite,
                'image'          => $doctor->user->profile_photo,
                'distance_km'    => round($doctor->distance, 2),
        ];
    });

  return  $this->successResponse([
            'user'     => [
                'id'       => $user->id,
                'name'     => $user->name,
                'greeting' =>  'Welcome back, ' . $user->name ,
            'location' => [
                'address'      => '12 El-Nasr Street, Cairo',
                'location_lat' => $user->location_lat,
                'location_lng' => $user->location_lng,
            ],
                'profile_photo' => $user->profile_photo,
            ],
            'specialties'       => $specialties,
            'doctors_near_you'  =>  $formattedDoctors
        ]);

    }catch(\Exception $e){
      return $this->handleException($e);
  }

    }
    // End Index

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
