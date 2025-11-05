<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateBookingRequest;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
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
        
        return view('admin.bookings.index', compact('bookings'));
    }

    public function show(int $id): View
    {
        $booking = Booking::with(['doctor.user', 'patient.user', 'payment', 'disputes'])->findOrFail($id);
        return view('admin.bookings.show', compact('booking'));
    }

    public function edit(int $id): View
    {
        $booking = Booking::with(['doctor.user', 'patient.user', 'payment'])->findOrFail($id);
        return view('admin.bookings.edit', compact('booking'));
    }

    public function update(UpdateBookingRequest $request, int $id): RedirectResponse
    {
        $booking = Booking::findOrFail($id);
        
        $booking->update([
            'date_time' => $request->date_time,
            'status' => $request->status,
            'price' => $request->price ?? $booking->price,
        ]);

        return redirect()
            ->route('admin.bookings.show', $booking->id)
            ->with('success', 'تم تحديث الحجز بنجاح');
    }

    public function updateStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,rescheduled',
        ]);

        $booking = Booking::findOrFail($id);
        $booking->update(['status' => $request->status]);

        return redirect()
            ->route('admin.bookings.show', $booking->id)
            ->with('success', 'تم تحديث حالة الحجز بنجاح');
    }

    public function destroy(int $id): RedirectResponse
    {
        $booking = Booking::findOrFail($id);
        
        // Only allow deletion if booking is not confirmed or is in the future
        if ($booking->status === 'confirmed' && $booking->date_time > now()) {
            return redirect()
                ->route('admin.bookings.index')
                ->with('error', 'لا يمكن حذف حجز مؤكد في المستقبل. يرجى إلغاؤه أولاً');
        }
        
        $booking->delete();

        return redirect()
            ->route('admin.bookings.index')
            ->with('success', 'تم حذف الحجز بنجاح');
    }
}


