<?php

namespace App\Services;

use App\Events\NotificationSent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    //  send notification to user 
    public static function sendToUser(User $user, string $title, string $body, string $type = null)
    {
        try {
            $notification = Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
                'type' => $type,
            ]);

           
            event(new NotificationSent($notification, "user.{$user->id}"));
        } catch (\Throwable $e) {
            Log::error('NotificationService@sendToUser failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }

    //  send notification to doctor 
    public static function sendToDoctor(User $doctorUser, string $title, string $body, string $type = null)
    {
        try {
            $notification = Notification::create([
                'user_id' => $doctorUser->id,
                'title' => $title,
                'body' => $body,
                'type' => $type,
            ]);

            event(new NotificationSent($notification, "doctor.{$doctorUser->id}"));
        } catch (\Throwable $e) {
            Log::error('NotificationService@sendToDoctor failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }

     //  send notification to Admin 
    public static function sendToAdmin(string $title, string $body, string $type = 'system')
    {
        try {
            $admins = User::where('role', 'admin')->get();

            foreach ($admins as $admin) {
                $notification = Notification::create([
                    'user_id' => $admin->id,
                    'title' => $title,
                    'body' => $body,
                    'type' => $type,
                ]);

                event(new NotificationSent($notification, "admin.{$admin->id}"));

            }
        } catch (\Throwable $e) {
            Log::error('NotificationService@sendToAdmin failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }
}
