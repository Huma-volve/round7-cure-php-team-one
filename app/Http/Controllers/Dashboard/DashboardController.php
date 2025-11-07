<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Review;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
        public function index()
    {
        $stats = [
            'doctors_count' => Doctor::count(),
            'patients_count' => Patient::count(),
            'bookings_count' => Booking::count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'total_earnings' => Payment::sum('amount'),
            'average_rating' => number_format(Review::avg('rating'), 1),

            //  'monthly_earnings' => Payment::whereMonth('created_at', now()->month)->sum('amount'),
            // 'annual_earnings' => Payment::whereYear('created_at', now()->year)->sum('amount'),
            // 'tasks_progress' => Booking::where('status', 'completed')->count() > 0
            //     ? round((Booking::where('status', 'completed')->count() / Booking::count()) * 100)
            //     : 0,
            // 'pending_requests' => Booking::where('status', 'pending')->count(),
        ];
          
        return view('admin.dashboard', compact('stats'));
    }
}
