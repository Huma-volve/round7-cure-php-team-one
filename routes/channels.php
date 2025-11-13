<?php
use Illuminate\Support\Facades\Broadcast;
use App\Models\Chat;


Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    // المستخدم لازم يكون طرف في الشات
    return $user->chats()->pluck('id')->contains((int) $chatId);
});

Broadcast::channel('admin.{id}', function ($user, $adminId) {
    return (int) $user->id === (int) $adminId;
});

Broadcast::channel('patient.{id}', function ($user, $Id) {
    return (int) $user->id === (int) $Id;
});

Broadcast::channel('doctor.{id}', function ($user, $Id) {
    return (int) $user->id === (int) $Id;
});
