<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Services\FavoriteService;
use Hamcrest\Core\Set;
use Illuminate\Support\Facades\Auth;
use PhpParser\Comment\Doc;

class DoctorService
{

    protected $favoriteService;
    protected $searchService;

    public function __construct(FavoriteService $favoriteService, SearchService $searchService)
    {
        $this->favoriteService = $favoriteService;
        $this->searchService  = $searchService;
    }


    public function getAllDoctors()
    {
        $doctors = Doctor::with('user', 'specialty')->paginate(6);
        //paginate  or get  9 in productoin

        return $doctors;
    } //end getAllDoctors

    public function searchDoctorsNearby(User $user, $latitude, $longitude, $search = null, $radius = 10)
    {

        $doctors = $this->searchService->searchDoctorsNearby($latitude, $longitude, $search, $radius, $user);

        return $doctors;
    } //end getNearbyDoctors


    public function getDoctorDetails($doctorId, $user = null)
    {
        try {

            $doctor = Doctor::with([
                'user',
                'specialty',
                'reviews.patient.user'
            ])->findOrFail($doctorId);

            $doctor->patient_count = $doctor->bookings()->count();
            $doctor->average_rating = $doctor->getAverageRatingAttribute();
            $doctor->reviews_count = $doctor->getReviewsCountAttribute();

            return $doctor;
        } catch (\Exception $e) {
            throw $e;
        }
    } //end getDoctorDetails


    public function showPatient($patientId)
    {

        $doctorId = Auth::user()->doctor->id;

        $patient =  Patient::with([
            'user:id,name,mobile,birthdate',
            'bookings' => function ($q)  use ($doctorId) {
                $q->where('doctor_id', $doctorId)
                    ->with('payment', 'review');
            }
        ])->whereRelation('bookings', 'doctor_id', $doctorId)
            ->find($patientId);

        return $patient;
    } // End showPatient

    public function earnings()
    {
        $doctorId = Auth::user()->doctor->id;

        $totalEarnings = Booking::where('doctor_id', $doctorId)
            ->where('status', 'confirmed')
            ->with('payment')
            ->get()
            ->sum(fn($booking) => $booking->payment->amount ?? 0);

        return $totalEarnings;
    } // End earnings


}
