<?php

namespace App\Services;

use App\Models\User;
use App\Models\Doctor;
use App\Models\SearchHistory;
use App\Services\FavoriteService;

class SearchService {
    protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }


  public function searchDoctors($latitude, $longitude, $search = null, $radius = 10 , User $user){

        $query = Doctor::with('specialty', 'user');

        if ($search) {
            $ids = Doctor::search($search )->get()->pluck('id');
            $query->whereIn('id', $ids);
        }



        if (!is_null($latitude) && !is_null($longitude)) {
            $query->selectRaw(
                "*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance",
                [$latitude, $longitude, $latitude]
            )
            ->havingRaw("distance < ?", [$radius])
            ->orderBy("distance", "asc")
            ->take(5);
        }

        return $query->get();
    } // end searchDoctors


      public function storeSearchHistory(User $user, array $data ){

      $exists = SearchHistory::where('search_query', $data['search_query'])
        ->where('user_id', $user->id)
        ->exists();

        if( !$exists)
        {
            SearchHistory::create([
            'user_id'        => $user->id,
            'search_query'   => $data['search_query'],
            'search_type'    => $data['search_type'] ?? 'general',
            'specialty_id'   => $data['specialty_id'] ?? null,
            'latitude'       => $data['latitude'] ?? null,
            'longitude'      => $data['longitude'] ?? null,
            'location_name'  => $data['location_name'] ?? null,
            'is_saved'       => true,
            'searched_at'    => now(),
            // other fields...
        ]);

        }


    }

}

