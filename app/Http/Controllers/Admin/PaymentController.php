<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
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
        
        return view('admin.payments.index', compact('payments'));
    }

    public function show(int $id): View
    {
        $payment = Payment::with(['booking.patient.user', 'booking.doctor.user', 'disputes'])->findOrFail($id);
        return view('admin.payments.show', compact('payment'));
    }

    public function refund(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $payment = Payment::findOrFail($id);
        
        // Only allow refund for successful payments
        if ($payment->status !== 'success') {
            return redirect()
                ->route('admin.payments.show', $payment->id)
                ->with('error', 'لا يمكن استرداد مبلغ غير ناجح');
        }

        // Update payment status to refunded
        $payment->update([
            'status' => 'refunded',
        ]);

        // Here you would typically integrate with payment gateway API to process refund
        // For now, we'll just update the status

        return redirect()
            ->route('admin.payments.show', $payment->id)
            ->with('success', 'تم معالجة طلب الاسترداد. سيتم استرداد المبلغ خلال 5-7 أيام عمل');
    }
}


