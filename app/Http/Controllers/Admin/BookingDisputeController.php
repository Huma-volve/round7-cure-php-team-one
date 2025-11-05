<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingDispute;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingDisputeController extends Controller
{
    public function index(Request $request): View
    {
        $query = BookingDispute::with(['booking.patient.user', 'booking.doctor.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $bookingDisputes = $query->orderByDesc('id')->paginate(15);
        
        return view('admin.disputes.index', [
            'paymentDisputes' => collect([]),
            'bookingDisputes' => $bookingDisputes,
        ]);
    }

    public function show(int $id): View
    {
        $dispute = BookingDispute::with(['booking.patient.user', 'booking.doctor.user'])->findOrFail($id);
        return view('admin.disputes.show', compact('dispute'));
    }
}


