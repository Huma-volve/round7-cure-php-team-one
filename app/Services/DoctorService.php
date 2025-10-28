<?php

namespace App\Services;
use App\Models\User;
use App\Models\Doctor;
use App\Services\FavoriteService;


class DoctorService  {

    protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    public function getNearbyDoctors(User $user, $latitude, $longitude, $search = null , $radius =10){

       $doctors = Doctor::with('user')->selectRaw(
            "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
             [$latitude, $longitude, $latitude]
        )
        ->when($search, function($query, $search) {
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%$search%");
            })
             ->orWhereHas('specialty', function($q) use ($search) {
               $q->where('name', 'like', "%$search%");
             });

        })
        ->havingRaw("distance < ?", [$radius])
        ->orderBy("distance", "asc")
        ->get();

        $doctors->each(function($doctor) use ($user) {
            $doctor->is_favorite = $this->favoriteService->isFavorite($user, $doctor);
        });

        return $doctors;


    } //end getNearbyDoctors


    public function getDoctorDetails($doctorId, $user = null){

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
