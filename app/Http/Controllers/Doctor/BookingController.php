<?php

namespace App\Http\Controllers\Doctor;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends BaseDoctorController
{
    public function index(Request $request): View
    {
        $doctor = $this->currentDoctor();
        $status = $request->query('status');

        $bookings = Booking::with(['patient.user'])
            ->where('doctor_id', $doctor->id)
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('date_time')
            ->paginate(15);

        return view('doctor.bookings.index', compact('bookings', 'status', 'doctor'));
    }
}

