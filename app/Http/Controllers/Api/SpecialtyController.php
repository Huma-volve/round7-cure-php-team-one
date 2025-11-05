<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index()
    {
        $specialties = Specialty::select('id', 'name','image')->get();

        return response()->json([
            'specialties' => $specialties
            ], 200);

    }
}
