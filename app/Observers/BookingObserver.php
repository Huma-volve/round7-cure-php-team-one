<?php
namespace App\Observers;

use App\Events\BookingCreated;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Notification;
use App\Services\NotificationService;

class BookingObserver
{
    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        
            $doctor = $booking->doctor;
            $patient = $booking->patient;
            $booking_id = $booking->id;
            // send to doctor
            NotificationService::sendToDoctor(
                $doctor->user,
                'New Booking',
                "You have a new booking from {$patient->user->name}",
                'booking',
                $booking_id
            );
            // send to user
        NotificationService::sendToUser(
                $patient->user,
                'Booking Created',
                "Your booking with Dr. {$doctor->user->name} has been created successfully.",
                'booking',
                $booking_id
            );
            // send to admin
            NotificationService::sendToAdmin(
                'System Log',
                "A new booking was created by {$patient->user->name}",
                'system',
                $booking_id
            );
            
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        $booking_id = $booking->id;

        if ($booking->isDirty('status')) {
            $old = $booking->getOriginal('status');
            $new = $booking->status;

            // send to patient depending on status
            if ($new === 'cancelled') {
                NotificationService::sendToUser(
                    $booking->patient->user,
                    'Booking Cancelled',
                    "Your booking with Dr. {$booking->doctor->user->name} has been cancelled.",
                    'booking',
                    $booking_id
                );
            }

            if ($new === 'rescheduled') {
                NotificationService::sendToUser(
                    $booking->patient->user,
                    'Booking Rescheduled',
                    "Your booking with Dr. {$booking->doctor->user->name} has been rescheduled.",
                    'booking',
                    $booking_id
                );
            }

            // always inform the doctor
            NotificationService::sendToDoctor(
                $booking->doctor->user,
                'Booking Updated',
                "Booking #{$booking->id} status changed from {$old} to {$new}.",
                'booking',
                $booking_id
            );
        }
    
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        //
        $doctor = $booking->doctor;
        $patient = $booking->patient;
        $booking_id = $booking->id;

        if (! $doctor || ! $patient) {
            return;
        }
        // send to user
        NotificationService::sendToUser(
            $patient->user,
            'Booking Deleted',
            "Your booking with Dr. {$doctor->user->name} has been deleted from the system.",
            'booking',
            $booking_id
        );

        // send to admin
        NotificationService::sendToDoctor(
            $doctor->user,
            'Booking Removed',
            "The booking with patient {$patient->user->name} has been deleted from the system.",
            'booking',
            $booking_id
        );

        // send to admin 
        NotificationService::sendToAdmin(
            'System Alert',
            "Booking #{$booking_id} between Dr. {$doctor->user->name} and {$patient->user->name} was deleted.",
            'system',
            $booking_id
        );
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
