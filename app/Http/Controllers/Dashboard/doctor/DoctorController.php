<?php

namespace App\Http\Controllers\Dashboard\doctor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function index()
    {
        $doctor = Auth::user() ?? User::find(4);

        $patients = Booking::where('doctor_id', $doctor->id)->with('patient.user')->get();


        return view('admin.doctors.index', compact('patients'));
    }
}
