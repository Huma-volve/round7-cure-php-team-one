<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingDispute;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PaymentDispute;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        // User Statistics
        $totalUsers = User::count();
        $totalPatients = User::whereHas('patient')->count();
        $totalDoctors = User::whereHas('doctor')->count();

        // Booking Statistics
        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();

        // Payment Statistics
        $todayPayments = Payment::whereDate('created_at', today())->where('status', 'success')->sum('amount');
        $monthlyPayments = Payment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'success')
            ->sum('amount');
        $yearlyPayments = Payment::whereYear('created_at', now()->year)
            ->where('status', 'success')
            ->sum('amount');

        // Dispute Statistics
        $openDisputes = PaymentDispute::where('status', 'pending')->count() + 
                       BookingDispute::where('status', 'pending')->count();
        $resolvedDisputes = PaymentDispute::where('status', 'resolved')->count() + 
                           BookingDispute::where('status', 'resolved')->count();
        $rejectedDisputes = PaymentDispute::where('status', 'rejected')->count() + 
                           BookingDispute::where('status', 'rejected')->count();

        // Chart Data - Bookings by Month (last 6 months)
        $bookingsByMonth = Booking::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Chart Data - Payments by Month (last 6 months)
        $paymentsByMonth = Payment::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'success')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Chart Data - Booking Status Distribution
        $bookingStatusData = Booking::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        // Chart Data - Payment Gateway Distribution
        $paymentGatewayData = Payment::select('gateway', DB::raw('COUNT(*) as count'))
            ->where('status', 'success')
            ->groupBy('gateway')
            ->pluck('count', 'gateway');

        // Upcoming Bookings
        $upcomingBookings = Booking::with(['doctor.user', 'patient.user'])
            ->where('date_time', '>=', now())
            ->where('status', 'confirmed')
            ->orderBy('date_time', 'asc')
            ->take(5)
            ->get();

        $user_id = Auth::id(); 
        $notifications = Notification::with('user')
         ->where('user_id', $user_id)
         ->orderBy('created_at','desc')
         ->get();

        $unreadCount = Notification::where('user_id', $user_id)
        ->where('is_read', false)
        ->count();   

        return view('admin.dashboard', compact(
            'totalUsers', 'totalPatients', 'totalDoctors',
            'totalBookings', 'confirmedBookings', 'pendingBookings', 'cancelledBookings',
            'todayPayments', 'monthlyPayments', 'yearlyPayments',
            'openDisputes', 'resolvedDisputes', 'rejectedDisputes',
            'bookingsByMonth', 'paymentsByMonth',
            'bookingStatusData', 'paymentGatewayData',
            'upcomingBookings','notifications','unreadCount'
        ));
    }
}
