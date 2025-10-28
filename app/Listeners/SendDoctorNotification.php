<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendDoctorNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingCreated $event): void
    {
        //
        $booking = $event->booking;

        Notification::create([
            'user_id' => $booking->doctor->user_id,
            'title' => 'New Booking Received',
            'body' => 'A new appointment has been booked by patient ' . $booking->patient->user->name . '.',
            'type' => 'booking_created',
            'booking_id' => $booking->id,
            'is_read' => false,
        ]);
    }
}
