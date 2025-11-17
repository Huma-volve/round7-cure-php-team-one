<?php

namespace App\Observers;

use App\Models\Message;
use App\Services\NotificationService;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        $message->load(['chat', 'sender', 'receiver']);

        $sender = $message->sender;
        $receiver = $message->receiver;
        $chat = $message->chat;

        if (!$sender || !$receiver || !$chat) {
            return;
        }

        NotificationService::sendToUser(
            $receiver,
            'New Message Received',
            "{$sender->name}: {$message->body}",
            'chat',
            $chat->id,
            'unread'
        );

        NotificationService::sendToAdmin(
            'New Message Sent',
            "{$sender->name} sent a message to {$receiver->name} in chat #{$chat->id}.",
            'chat',
            $chat->id,
            'info'
        );
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
         if ($message->isDirty('body')) {
            $message->load(['sender', 'receiver', 'chat']);
            NotificationService::sendToUser(
                $message->receiver,
                'Message Updated',
                "{$message->sender->name} updated the message: {$message->body}",
                'chat',
                $message->chat->id,
                'unread'
            );
        }
    }

    /**
     * Handle the Message "deleted" event.
     */
    public function deleted(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "restored" event.
     */
    public function restored(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "force deleted" event.
     */
    public function forceDeleted(Message $message): void
    {
        //
    }
}
