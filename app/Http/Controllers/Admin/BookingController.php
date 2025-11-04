<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['doctor.user', 'patient.user', 'payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('date_time', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('date_time', '<=', $request->date('date_to'));
        }

        $bookings = $query->orderByDesc('id')->paginate(15);
        return response()->json($bookings);
    }

    public function show(int $id)
    {
        $booking = Booking::with(['doctor.user', 'patient.user', 'payment', 'disputes'])->findOrFail($id);
        return response()->json($booking);
    }
}


