<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function show($user_id)
    {
        try {

            //  Check if user exists
            $userExists = User::where('id', $user_id)->exists();
            if (!$userExists) {
                return ApiResponse::error(null, 'User not found.', 404);
            }

            $notifications = Notification::where('user_id', $user_id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            if ($notifications->isEmpty()) {
                return ApiResponse::success(['notifications' => []], 'No notifications found for this user.');
            }

            return ApiResponse::success(['notifications' => $notifications], 'Notifications fetched successfully');
        } catch (\Throwable $e) {

            Log::error('NotificationController@show failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            return ApiResponse::error(null, 'An unexpected error occurred while fetching notifications.', 500);
        }
    }

    public function store(StoreNotificationRequest $request)
    {
        $data = $request->validated();

        try {

            $doctor = Doctor::find($request->doctor_id);
            $patient = auth()->user()->patient;

            if (! $doctor || ! $patient) {
                return ApiResponse::error(null, 'Doctor or patient not found', 404);
            }

            $booking = Booking::create([
                'doctor_id' => $doctor->id,
                'patient_id' => $patient->id,
                'date_time' => $request->date_time,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'price' => $doctor->session_price,
            ]);

            // send to doctor
            NotificationService::sendToDoctor(
                $doctor->user,
                'New Booking',
                "You have a new booking from {$patient->user->name}",
                'booking'
            );
            // send to user
            NotificationService::sendToUser(
                $patient->user,
                'Booking Created',
                "Your booking with Dr. {$doctor->user->name} has been created successfully.",
                'booking'
            );
            // send to admin
            NotificationService::sendToAdmin(
                'System Log',
                "A new booking was created by {$patient->user->name}",
                'system'
            );

            return ApiResponse::success([], 'Booking created successfully and notification sent automatically.');
        } catch (\Throwable $e) {
            Log::error('NotificationController@store failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            return ApiResponse::error(null, 'An unexpected error occurred while store notifications.', 500);
        }
    }

    // mark notification by (notification id )
    public function markAsRead($id)
    {
        try {
            $notification = Notification::find($id);

            if (! $notification) {
                return ApiResponse::error(null, 'Notification not found', 404);
            }

            $notification->update(['is_read' => true]);

            return ApiResponse::success(null, 'Notification marked as read', 200);
        } catch (\Throwable $e) {
            Log::error('NotificationController@markAsRead failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            return ApiResponse::error(null, 'An unexpected error occurred while marked notifications.', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $notification = Notification::find($id);
            if (! $notification) {
                return ApiResponse::error(null, 'Notification not found.', 404);
            }
            $notification->delete();


            return ApiResponse::success(null, 'Notification deleted successfully');
        } catch (\Throwable $e) {
            Log::error('NotificationController@destroy failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            return ApiResponse::error(null, 'An unexpected error occurred while deleted notifications.', 500);
        }
    }

//  move it into BookingController

    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);
        $oldStatus = $booking->status;

        $booking->update(['status' => $request->status]);

        if ($request->status === 'cancelled') {
            NotificationService::sendToUser(
                $booking->patient->user,
                'Booking Cancelled',
                "Your booking with Dr. {$booking->doctor->user->name} has been cancelled.",
                'booking'
            );
        }

        if ($request->status === 'rescheduled') {
            NotificationService::sendToUser(
                $booking->patient->user,
                'Booking Rescheduled',
                "Your booking with Dr. {$booking->doctor->user->name} has been rescheduled.",
                'booking'
            );
        }

        return ApiResponse::success($booking, 'Booking updated successfully.');
    }
}
