<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
      public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function broadcastOn(): array
    {
      
        return [
            new Channel('doctor.' . $this->booking->doctor_id),
            new Channel('patient.' . $this->booking->patient_id),
            new Channel('admin.' . $this->booking->user_id),
        ];
    }

    public function broadcastAs()
    {
        return 'booking.created';
    }

     public function broadcastWith(): array
    {
        return [
            'id' => $this->booking->id,
            'doctor_id' => $this->booking->doctor_id,
            'patient_id' => $this->booking->patient_id,
            'date_time' => $this->booking->date_time,
            'status' => $this->booking->status,
        ];
    }
}
