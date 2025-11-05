<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentDispute;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentDisputeController extends Controller
{
    public function index(Request $request): View
    {
        $query = PaymentDispute::with(['payment.booking.patient.user', 'payment.booking.doctor.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('reason')) {
            $query->where('reason', $request->string('reason'));
        }

        $paymentDisputes = $query->orderByDesc('id')->paginate(15);
        
        return view('admin.disputes.index', [
            'paymentDisputes' => $paymentDisputes,
            'bookingDisputes' => collect([]),
        ]);
    }

    public function show(int $id): View
    {
        $dispute = PaymentDispute::with(['payment.booking.patient.user', 'payment.booking.doctor.user'])->findOrFail($id);
        return view('admin.disputes.show', compact('dispute'));
    }
}


