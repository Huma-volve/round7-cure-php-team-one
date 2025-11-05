<?php

namespace App\Services;

use App\Events\NotificationSent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Event;

class NotificationService
{
    //  send notification to user 
    public static function sendToUser(User $user, string $title, string $body, string $type = null, $booking_id = null)
    {
        try {
            $notification = Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
                'booking_id' => $booking_id,
                'type' => $type,
            ]);

            Event::dispatch(new NotificationSent($notification, "user.{$user->id}"));

            return $notification;

        } catch (\Throwable $e) {
            Log::error('NotificationService@sendToUser failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }

    //  send notification to doctor 
    public static function sendToDoctor(User $doctorUser, string $title, string $body, string $type = null, $booking_id = null)
    {
        try {
            $notification = Notification::create([
                'user_id' => $doctorUser->id,
                'title' => $title,
                'body' => $body,
                'booking_id' => $booking_id,
                'type' => $type,
            ]);

            // event(new NotificationSent($notification, "doctor.{$doctorUser->id}"));
            Event::dispatch(new NotificationSent($notification, "doctor.{$doctorUser->id}"));
            return $notification;
        } catch (\Throwable $e) {
            Log::error('NotificationService@sendToDoctor failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
        }
    }

     //  send notification to Admin 
    public static function sendToAdmin(string $title, string $body, string $type = 'system',  $booking_id = null)
    {
        try {
            $admins = User::role('admin')->get();

            foreach ($admins as $admin) {
                $notification = Notification::create([
                    'user_id' => $admin->id,
                    'title' => $title,
                    'body' => $body,
                    'booking_id' => $booking_id,
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
