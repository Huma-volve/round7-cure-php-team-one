<?php

namespace App\Http\Controllers\Doctor;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\View\View;

class DashboardController extends BaseDoctorController
{
    public function index(): View
    {
        $doctor = $this->currentDoctor();

        $bookingsQuery = Booking::with(['patient.user'])
            ->where('doctor_id', $doctor->id);

        $recentBookings = (clone $bookingsQuery)
            ->orderByDesc('date_time')
            ->limit(5)
            ->get();

        $stats = [
            'total_bookings' => (clone $bookingsQuery)->count(),
            'upcoming_bookings' => (clone $bookingsQuery)->where('date_time', '>=', now())->count(),
            'patients_count' => (clone $bookingsQuery)->distinct('patient_id')->count('patient_id'),
            'earnings' => Payment::whereHas('booking', function ($query) use ($doctor) {
                $query->where('doctor_id', $doctor->id);
            })->sum('amount'),
        ];

        return view('doctor.dashboard', compact('doctor', 'stats', 'recentBookings'));
    }
}

