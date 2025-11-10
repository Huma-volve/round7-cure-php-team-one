<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\SearchHistory;
use App\Services\FavoriteService;
use Clue\Redis\Protocol\Model\Request;

class SearchService {
    protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }



  public function searchDoctorsNearby($latitude, $longitude, $search = null, $radius = 10 , User $user){

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

  public function SearchHistory(User $user){

        return SearchHistory::with('user')->where('user_id', $user->id)
            ->orderBy('searched_at', 'desc')
            ->get();
    } // end getSearchHistory

  public function  distorySearch($id){

      $history =  SearchHistory::find($id);

      if(!$history){
        return false ;
      }

         $history->delete();
        return true;
  }



  public function clearSearchHistory(User $user){

        return SearchHistory::where('user_id', $user->id)->delete();
    } // end clearSearchHistory












     /*/
                      يبحث الدكتور على المرضى خاصتة
     */
public function searchDoctorPatients($doctorId, $searchTerm = null)
{
    $query = Patient::whereHas('bookings', function ($q) use ($doctorId) {
            $q->where('doctor_id', $doctorId);
        })
        ->with([
            'user',
            'bookings' => function ($q) use ($doctorId) {
                $q->where('doctor_id', $doctorId);
            }
        ]);


    if (!empty($searchTerm)) {
        $userIds = User::search($searchTerm)->get()->pluck('id');

        if ($userIds->isNotEmpty()) {
            $query->whereIn('user_id', $userIds);
        } else {

            return collect();
        }
    }


    return $query->distinct()->take(6)->get();
}



}

