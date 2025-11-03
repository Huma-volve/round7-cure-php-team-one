<?php
use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat;

 
Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // المستخدم لازم يكون طرف في الشات
    return $user->chats()->pluck('id')->contains((int) $chatId);
});
