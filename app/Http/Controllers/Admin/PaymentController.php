<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with(['booking.patient.user', 'booking.doctor.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('gateway')) {
            $query->where('gateway', $request->string('gateway'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date('date_to'));
        }

        if ($request->filled('min_amount')) {
            $query->where('amount', '>=', (float) $request->input('min_amount'));
        }

        if ($request->filled('max_amount')) {
            $query->where('amount', '<=', (float) $request->input('max_amount'));
        }

        $payments = $query->orderByDesc('id')->paginate(15);
        return response()->json($payments);
    }

    public function show(int $id)
    {
        $payment = Payment::with(['booking.patient.user', 'booking.doctor.user', 'disputes'])->findOrFail($id);
        return response()->json($payment);
    }
}


