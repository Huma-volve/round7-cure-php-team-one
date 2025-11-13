<?php

namespace App\Observers;

use App\Models\Chat;
use App\Services\NotificationService;

class ChatObserver
{
    /**
     * Handle the Chat "created" event.
     */
    public function created(Chat $chat): void
    {
        $chat->load(['userOne', 'userTwo']);

        $userOne = $chat->userOne;
        $userTwo = $chat->userTwo;

        if (!$userOne || !$userTwo) {
            return;
        }

        NotificationService::sendToUser(
            $userOne,
            'New Chat Started',
            "You have started a chat with {$userTwo->name}.",
            'chat',
            $chat->id,
            'active'
        );

        NotificationService::sendToUser(
            $userTwo,
            'New Chat Started',
            "{$userOne->name} has started a chat with you.",
            'chat',
            $chat->id,
            'active'
        );

        NotificationService::sendToAdmin(
            'New Chat Created',
            "{$userOne->name} and {$userTwo->name} have started a new chat.",
            'chat',
            $chat->id,
            'info'
        );
    }

    /**
     * Handle the Chat "updated" event.
     */
    public function updated(Chat $chat): void
    {
        //
        if ($chat->isDirty('last_message')) {
            $chat->load(['userOne', 'userTwo']);
            $userOne = $chat->userOne;
            $userTwo = $chat->userTwo;

            if (!$userOne || !$userTwo) {
                return;
            }

            NotificationService::sendToUser(
                $userTwo,
                'New Message',
                "{$userOne->name}: {$chat->last_message}",
                'chat',
                $chat->id,
                'unread'
            );
        }
    }

    /**
     * Handle the Chat "deleted" event.
     */
    public function deleted(Chat $chat): void
    {
        //
    }

    /**
     * Handle the Chat "restored" event.
     */
    public function restored(Chat $chat): void
    {
        //
    }

    /**
     * Handle the Chat "force deleted" event.
     */
    public function forceDeleted(Chat $chat): void
    {
        //
    }
}
