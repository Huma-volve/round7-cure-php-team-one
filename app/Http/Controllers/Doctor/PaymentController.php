<?php

namespace App\Http\Controllers\Doctor;

use App\Models\Payment;
use Illuminate\View\View;

class PaymentController extends BaseDoctorController
{
    public function index(): View
    {
        $doctor = $this->currentDoctor();

        $payments = Payment::with(['booking.patient.user'])
            ->whereHas('booking', function ($query) use ($doctor) {
                $query->where('doctor_id', $doctor->id);
            })
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('doctor.payments.index', compact('payments', 'doctor'));
    }
}

