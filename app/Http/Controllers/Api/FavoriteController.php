<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\User;
use App\Services\FavoriteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
       protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    public function toggleFavorite(Doctor $doctor)
    {
        try{
        $user = Auth::user() ;
        $result = $this->favoriteService->toggleFavorite($user, $doctor);

        return ApiResponse::success([
            'status' => $result['status'],
            'message' => "Favorite {$result['status']} successfully",
            'doctor_id' => $result['doctor_id']
        ]);
        }catch (\Exception $e){
            return ApiResponse::error(null, $e->getMessage(), 500);
        }
    }

    public function getFavorites()
    {
        try{

        $user = Auth::user();
        $favorites = $this->favoriteService->getFavorites($user);

        if (!$favorites) {
            return ApiResponse::error(null, 'No favorite doctors found.', 404);
        }

        return ApiResponse::success(['favorites' => $favorites]);

        }catch (\Exception $e){
            return ApiResponse::error(null, $e->getMessage(), 500);
       }
    }

    public function checkFavorite(Doctor $doctor)
    {
        try{
        $user = Auth::user() ;
        $isFavorite = $this->favoriteService->isFavorite($user, $doctor);

        if (!$isFavorite) {
            return ApiResponse::error(null, 'Doctor is not a favorite.', 404);
        }
        return ApiResponse::success(['is_favorite' => $isFavorite]);
        }catch (\Exception $e){
            return ApiResponse::error(null, $e->getMessage(), 500);
        }
    }
}
