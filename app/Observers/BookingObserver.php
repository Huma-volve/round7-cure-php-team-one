<?php
namespace App\Observers;

use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Notification;
use App\Events\BookingCreated;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        
        Notification::create([
            'user_id' => $booking->doctor->user_id,
            'title' => 'New Booking Received',
            'body' => 'A new appointment has been booked by patient ' . $booking->patient->user->name . '.',
            'type' => 'booking_created',
            'booking_id' => $booking->id,
            'is_read' => false,
        ]);

         Notification::create([
                'user_id' => $booking->patient->user_id, 
                'title' => 'New Booking Created',
                'body' => 'Your appointment with Dr. ' . $booking->doctor->user->name . ' has been successfully booked.',
                'type' => 'booking_created',
                'booking_id' => $booking->id,
                'is_read' => false,
            ]);

        
        broadcast(new BookingCreated($booking))->toOthers();

    }


    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        //
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        //
    }
}
