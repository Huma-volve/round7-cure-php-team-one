<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Requests\RescheduleBookingRequest;
use App\Models\Booking;
use App\Repositories\BookingRepository;
use App\Services\Booking\BookingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends BaseDoctorController
{
    public function __construct(
        private BookingService $bookingService,
        private BookingRepository $bookingRepository
    ) {}

    public function index(Request $request): View
    {
        $doctor = $this->currentDoctor();
        $status = $request->query('status');

        $bookings = Booking::with(['patient.user'])
            ->where('doctor_id', $doctor->id)
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderByDesc('date_time')
            ->paginate(15);

        return view('doctor.bookings.index', compact('bookings', 'status', 'doctor'));
    }

    /**
     * عرض تفاصيل الحجز
     */
    public function show($id): View
    {
        $doctor = $this->currentDoctor();
        $booking = $this->bookingRepository->findByIdWithRelations($id);

        if (!$booking) {
            abort(404, __('messages.booking.not_found'));
        }

        if ($booking->doctor_id !== $doctor->id) {
            abort(403, __('messages.booking.unauthorized'));
        }

        return view('doctor.bookings.show', compact('booking', 'doctor'));
    }

    /**
     * تأكيد الحجز
     */
    public function confirm($id): RedirectResponse
    {
        $doctor = $this->currentDoctor();
        $booking = $this->bookingRepository->findByIdWithRelations($id);

        if (!$booking) {
            return redirect()->route('doctor.bookings.index')
                ->with('error', __('messages.booking.not_found'));
        }

        if ($booking->doctor_id !== $doctor->id) {
            return redirect()->route('doctor.bookings.index')
                ->with('error', __('messages.booking.unauthorized'));
        }

        try {
            $this->bookingService->confirmBooking($booking);
            
            return redirect()->route('doctor.bookings.index')
                ->with('success', __('messages.booking.confirmed'));
        } catch (\Exception $e) {
            return redirect()->route('doctor.bookings.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * إلغاء الحجز
     */
    public function cancel($id): RedirectResponse
    {
        $doctor = $this->currentDoctor();
        $booking = $this->bookingRepository->findByIdWithRelations($id);

        if (!$booking) {
            return redirect()->route('doctor.bookings.index')
                ->with('error', __('messages.booking.not_found'));
        }

        if ($booking->doctor_id !== $doctor->id) {
            return redirect()->route('doctor.bookings.index')
                ->with('error', __('messages.booking.unauthorized'));
        }

        try {
            $this->bookingService->cancelBooking($booking);
            
            return redirect()->route('doctor.bookings.index')
                ->with('success', __('messages.booking.cancelled'));
        } catch (\Exception $e) {
            return redirect()->route('doctor.bookings.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * إعادة جدولة الحجز
     */
    public function reschedule(RescheduleBookingRequest $request, $id): RedirectResponse
    {
        $doctor = $this->currentDoctor();
        $booking = $this->bookingRepository->findByIdWithRelations($id);

        if (!$booking) {
            return redirect()->route('doctor.bookings.index')
                ->with('error', __('messages.booking.not_found'));
        }

        if ($booking->doctor_id !== $doctor->id) {
            return redirect()->route('doctor.bookings.index')
                ->with('error', __('messages.booking.unauthorized'));
        }

        try {
            $this->bookingService->rescheduleBooking($booking, $request->date_time);
            
            return redirect()->route('doctor.bookings.index')
                ->with('success', __('messages.booking.rescheduled'));
        } catch (\Exception $e) {
            return redirect()->route('doctor.bookings.index')
                ->with('error', $e->getMessage());
        }
    }
}

