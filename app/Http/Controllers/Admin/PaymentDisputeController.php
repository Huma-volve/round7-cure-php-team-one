<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentDispute;
use Illuminate\Http\Request;

class PaymentDisputeController extends Controller
{
    public function index(Request $request)
    {
        $query = PaymentDispute::with(['payment.booking.patient.user', 'payment.booking.doctor.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('reason')) {
            $query->where('reason', $request->string('reason'));
        }

        $disputes = $query->orderByDesc('id')->paginate(15);
        return response()->json($disputes);
    }

    public function show(int $id)
    {
        $dispute = PaymentDispute::with(['payment.booking.patient.user', 'payment.booking.doctor.user'])->findOrFail($id);
        return response()->json($dispute);
    }
}


