<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;

abstract class BaseDoctorController extends Controller
{
    protected function currentDoctor(): Doctor
    {
        $user = auth()->user();

        if (!$user || !$user->hasRole('doctor')) {
            abort(403);
        }

        $doctor = $user->doctor;

        if (!$doctor) {
            abort(403, 'Doctor profile not found.');
        }

        return $doctor;
    }
}

