<?php

namespace App\Services;
use App\Models\User;
use App\Models\Doctor;
use App\Services\FavoriteService;
use Hamcrest\Core\Set;

class DoctorService  {

    protected $favoriteService;
    protected $searchService;

    public function __construct(FavoriteService $favoriteService , SearchService $searchService)
    {
        $this->favoriteService = $favoriteService;
        $this->searchService  = $searchService;
    }


    public function getAllDoctors(){

        $doctors = Doctor::with('user', 'specialty')->paginate(4);
        //paginate  or get  9 in productoin

        return $doctors;
    } //end getAllDoctors

    public function getNearbyDoctors(User $user, $latitude, $longitude, $search = null , $radius =10){

        $doctors = $this->searchService->searchDoctors($latitude, $longitude, $search, $radius , $user);

        return $doctors;
    } //end getNearbyDoctors


    public function getDoctorDetails($doctorId, $user = null){
    //    dd('here');
        $doctor = Doctor::with([
            'user',
            'specialty',
            'reviews.patient.user'
        ] )->findOrFail($doctorId);

        // $patient = Booking::where('doctor_id', $doctorId)
        //     ->where('patient_id', $user?->patient?->id)
        //     ->where('status', 'completed')
        //     ->first();

        $doctor->patient_count = $doctor->bookings()->count();

        $doctor->average_rating = $doctor->getAverageRatingAttribute();
        $doctor->reviews_count = $doctor->getReviewsCountAttribute();

        return $doctor ;
    } //end getDoctorDetails




}
