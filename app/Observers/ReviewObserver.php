<?php

namespace App\Observers;

use App\Models\Review;
use App\Services\NotificationService;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    public function created(Review $review): void
    {
        $review->load(['doctor.user', 'patient.user']);

      
        NotificationService::sendToDoctor(
            $review->doctor->user,
            'New Review',
            "{$review->patient->user->name} has left a new review for you.",
            'review',
            $review->booking->id,
            'Completed'
        );

        NotificationService::sendToUser(
            $review->patient->user,
            'Review Added',
            "Your review for Dr. {$review->doctor->user->name} has been submitted.",
            'review',
            $review->booking->id,
            'Completed'
        );

        NotificationService::sendToAdmin(
            'Review Added',
            "Your review for Dr. {$review->patient->user->name} has been submitted to Dr. {$review->doctor->user->name}.",
            'system',
            $review->booking->id,
            'Completed'
        );


    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        //
        $review->load(['doctor.user', 'patient.user']);

      
        NotificationService::sendToDoctor(
            $review->doctor->user,
            'ُUpdate Review',
            "{$review->patient->user->name} updated review for you.",
            'review',
            $review->booking->id,
            'Completed'
        );

        NotificationService::sendToUser(
            $review->patient->user,
            'ُUpdate Review',
            "Your review for Dr. {$review->doctor->user->name} has been updated.",
            'review',
            $review->booking->id,
            'Completed'
        );
    }
    

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "restored" event.
     */
    public function restored(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "force deleted" event.
     */
    public function forceDeleted(Review $review): void
    {
        //
    }
}
