<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Services\FavoriteService;
use App\Models\User;
use App\Services\DoctorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

   protected $doctorService;
   protected $favoriteService;

    public function __construct(DoctorService $doctorService , FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
        $this->doctorService = $doctorService;
    }

    public function index(Request $request)
    {

    $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    $user = Auth::user() ?? User::find(1);
    $lat = $request->latitude ?? 30.0444; // Cairo center
    $lng = $request->longitude ?? 31.2357;
    $search = $request->input('search');


    $doctors = $this->doctorService->getNearbyDoctors($user, $lat, $lng, $search);

       return response()->json(['data' => $doctors]);

    }



}
