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

        $doctor->average_rating = $doctor->getAverageRatingAttribute();
        $doctor->reviews_count = $doctor->getReviewsCountAttribute();

        return $doctor;
    } //end getDoctorDetails




}
