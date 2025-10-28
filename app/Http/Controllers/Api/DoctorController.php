<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DoctorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{

       protected $doctorService;

    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function show($id, Request $request)
    {
        $user = Auth::user() ?? null; // ممكن تكون null لو العام لاسوء
        $doctor = $this->doctorService->getDoctorDetails($id, $user);



        return response()->json([
            
            'status' => true,
            'message' => 'Doctor data loaded successfully',
            'data' => $doctor
        ]);
    }

}
