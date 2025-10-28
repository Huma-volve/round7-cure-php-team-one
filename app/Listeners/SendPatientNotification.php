<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;


class SendPatientNotification
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
                'user_id' => $booking->patient->user_id, 
                'title' => 'New Booking Created',
                'body' => 'Your appointment with Dr. ' . $booking->doctor->user->name . ' has been successfully booked.',
                'type' => 'booking_created',
                'booking_id' => $booking->id,
                'is_read' => false,
            ]);
    }
}
