<?php

namespace App\Services;

use App\Models\Notification;

class DoctorNotificationService
{
    public function getDoctorNotifications($doctorId, $perPage = 10)
    {
        return Notification::where('user_id', $doctorId)
            ->whereIn('type', ['booking', 'review', 'chat'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getUnreadNotifications($doctorId)
    {
        return Notification::where('user_id', $doctorId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAsRead($doctorId, $notificationId)
    {
        $notification = Notification::where('user_id', $doctorId)
            ->where('id', $notificationId)
            ->first();

        if (! $notification) {
            return null;
        }

        $notification->update(['is_read' => true]);
        return $notification;
    }

    public function markAllAsRead($doctorId)
    {
        return Notification::where('user_id', $doctorId)
            ->where('is_read', 0)
            ->update(['is_read' => true]);
    }
}
