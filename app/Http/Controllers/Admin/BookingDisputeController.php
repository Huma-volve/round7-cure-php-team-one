<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingDispute;
use Illuminate\Http\Request;

class BookingDisputeController extends Controller
{
    public function index(Request $request)
    {
        $query = BookingDispute::with(['booking.patient.user', 'booking.doctor.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        $disputes = $query->orderByDesc('id')->paginate(15);
        return response()->json($disputes);
    }

    public function show(int $id)
    {
        $dispute = BookingDispute::with(['booking.patient.user', 'booking.doctor.user'])->findOrFail($id);
        return response()->json($dispute);
    }
}


