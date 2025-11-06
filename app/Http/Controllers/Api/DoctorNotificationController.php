<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Services\DoctorNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorNotificationController extends Controller
{
    protected $notificationService;

    public function __construct(DoctorNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    private function getAuthenticatedDoctor()
    {
        $user = Auth::user();

        if (! $user) {
            return ApiResponse::error(null, 'Unauthorized: no logged-in user.', 401);
        }

        $doctor = $user->doctor;
        if (! $doctor) {
            return ApiResponse::error(null, 'Doctor not found for this user.', 404);
        }

        return $doctor;
    }

    public function index()
    {
        $doctor = $this->getAuthenticatedDoctor();
        // $doctor->user_id= 17;
        $notifications = $this->notificationService->getDoctorNotifications($doctor->user_id);
        return ApiResponse::success(['notifications' => $notifications], 'Doctor notifications fetched successfully');
    }

    public function unread()
    {
        $doctor = $this->getAuthenticatedDoctor();
        
        $notifications = $this->notificationService->getUnreadNotifications($doctor->user_id);
        return ApiResponse::success(['notifications' => $notifications], 'Unread notifications fetched successfully.');
    }

    public function markAsRead($id) {
        $doctor = $this->getAuthenticatedDoctor();

        $notification = $this->notificationService->markAsRead($doctor->user_id, $id);
        if (! $notification) {
            return ApiResponse::error(null, 'Notification not found for this doctor.', 404);
        }

        return ApiResponse::success($notification, 'Notification marked as read successfully.');
    }

    public function markAllAsRead() {
        $doctor = $this->getAuthenticatedDoctor();
        if ($doctor instanceof \Illuminate\Http\JsonResponse) return $doctor;

        $updated = $this->notificationService->markAllAsRead($doctor->user_id);

        if ($updated === 0) {
            return ApiResponse::success([], 'No unread notifications found.');
        }

        return ApiResponse::success(null, 'All notifications marked as read successfully.');
    }
}
