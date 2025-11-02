<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    
    public $notification;
    public $channelName;

    public function __construct($notification, $channelName)
    {
        $this->notification = $notification;
        $this->channelName = $channelName;
    }

    public function broadcastOn()
    {
        return new Channel($this->channelName);
    }

    public function broadcastAs()
    {
        return 'notification.sent';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'title' => $this->notification->title,
            'body' => $this->notification->body,
            'user_id' => $this->notification->user_id,
            'type' => $this->notification->type,
            'is_read' => $this->notification->is_read,
        ];
    }
}
