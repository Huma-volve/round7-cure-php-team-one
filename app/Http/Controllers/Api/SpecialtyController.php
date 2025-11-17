<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Specialty;
use Illuminate\Http\Request;

class SpecialtyController extends Controller
{
    public function index()
    {
        
    $specialties = Specialty::select('id', 'name', 'image')->get()->map(function ($item) {
        // لو الصورة عندك في public/storage
        if ($item->image && !str_starts_with($item->image, 'http')) {
            $item->image = asset('storage/specialties/' . $item->image);
        }

        return $item;
    });

    return response()->json([
        'specialties' => $specialties
    ]);

    }
}
