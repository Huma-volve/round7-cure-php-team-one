<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * The channel the event should broadcast on.
     */
    public function broadcastOn()
    {
        // قناة عامة (أي شخص يقدر يسمعها)
        return new Channel('chat');
    }

    /**
     * (اختياري) لو عاوز اسم الحدث يظهر في الـ frontend باسم معين
     */
    public function broadcastAs()
    {
        return 'message.sent';
    }
}
