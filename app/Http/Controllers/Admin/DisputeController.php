<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResolveDisputeRequest;
use App\Models\PaymentDispute;
use App\Models\BookingDispute;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class DisputeController extends Controller
{
    public function index(Request $request): View
    {
        $paymentQuery = PaymentDispute::with(['payment.booking.patient.user', 'payment.booking.doctor.user']);
        $bookingQuery = BookingDispute::with(['booking.patient.user', 'booking.doctor.user']);

        if ($request->filled('status')) {
            $paymentQuery->where('status', $request->string('status'));
            $bookingQuery->where('status', $request->string('status'));
        }

        if ($request->filled('reason')) {
            $paymentQuery->where('reason', $request->string('reason'));
        }

        if ($request->filled('type')) {
            $bookingQuery->where('type', $request->string('type'));
        }

        $paymentDisputes = $paymentQuery->orderByDesc('id')->paginate(15);
        $bookingDisputes = $bookingQuery->orderByDesc('id')->paginate(15);

        return view('admin.disputes.index', compact('paymentDisputes', 'bookingDisputes'));
    }

    public function show(string $type, int $id): View
    {
        if ($type === 'payment') {
            $dispute = PaymentDispute::with(['payment.booking.patient.user', 'payment.booking.doctor.user'])->findOrFail($id);
        } else {
            $dispute = BookingDispute::with(['booking.patient.user', 'booking.doctor.user'])->findOrFail($id);
        }

        // Get notes from dispute_notes table if exists
        $notes = DB::table('dispute_notes')
            ->where('dispute_type', $type)
            ->where('dispute_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.disputes.show', compact('dispute', 'type', 'notes'));
    }

    public function resolve(ResolveDisputeRequest $request, string $type, int $id): RedirectResponse
    {
        if ($type === 'payment') {
            $dispute = PaymentDispute::findOrFail($id);
        } else {
            $dispute = BookingDispute::findOrFail($id);
        }

        $dispute->update([
            'status' => $request->action === 'resolve' ? 'resolved' : 'rejected',
            'resolution_notes' => $request->resolution_notes,
        ]);

        // Add admin note
        DB::table('dispute_notes')->insert([
            'dispute_type' => $type,
            'dispute_id' => $id,
            'user_id' => auth()->id(),
            'note' => $request->resolution_notes,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.disputes.show', [$type, $id])
            ->with('success', 'تم ' . ($request->action === 'resolve' ? 'حل' : 'رفض') . ' النزاع بنجاح');
    }

    public function addNote(Request $request, string $type, int $id): RedirectResponse
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        DB::table('dispute_notes')->insert([
            'dispute_type' => $type,
            'dispute_id' => $id,
            'user_id' => auth()->id(),
            'note' => $request->note,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.disputes.show', [$type, $id])
            ->with('success', 'تم إضافة الملاحظة بنجاح');
    }
}
