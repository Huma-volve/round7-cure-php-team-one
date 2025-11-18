<?php

namespace App\Http\Controllers\Doctor;

use App\Models\Patient;
use Illuminate\View\View;

class PatientController extends BaseDoctorController
{
    public function index(): View
    {
        $doctor = $this->currentDoctor();

        $patients = Patient::with('user')
            ->whereHas('bookings', function ($query) use ($doctor) {
                $query->where('doctor_id', $doctor->id);
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('doctor.patients.index', compact('patients', 'doctor'));
    }
}

