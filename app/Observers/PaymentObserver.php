<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\NotificationService;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     */
        public function created(Payment $payment): void
        {
            if ($payment->status !== 'success') {
            return;
        }

        $booking = $payment->booking;

        if (!$booking || !$booking->patient || !$booking->doctor) {
            return;
        }

        $patient = $booking->patient;
        $doctor = $booking->doctor;
        $amount = $payment->amount;

        NotificationService::sendToUser(
            $patient->user,
            'Payment Successful',
            "Your payment of {$amount} EGP for your booking with Dr. {$doctor->user->name} has been completed successfully.",
            'payment',
            $booking->id,
            'completed'
        );

        NotificationService::sendToAdmin(
            'New Payment Received',
            "{$patient->user->name} has completed a payment of {$amount} EGP for Dr. {$doctor->user->name}.",
            'payment',
            $booking->id,
            'completed'
        );
    }

    /**
     * Handle the Payment "updated" event.
     */
    public function updated(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "deleted" event.
     */
    public function deleted(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "restored" event.
     */
    public function restored(Payment $payment): void
    {
        //
    }

    /**
     * Handle the Payment "force deleted" event.
     */
    public function forceDeleted(Payment $payment): void
    {
        //
    }
}
