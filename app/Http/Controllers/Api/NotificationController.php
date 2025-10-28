<?php

namespace App\Http\Controllers\Api;

use App\Events\BookingCreated;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    public function show($user_id)
    {
        try{

            //  Check if user exists
            $userExists = User::where('id', $user_id)->exists();
            if (!$userExists) {
                return ApiResponse::error(null, 'User not found.', 404);
            }
            
            $notifications = Notification::where('user_id',$user_id)
            ->orderBy('created_at', 'desc')
                ->get();
            
            if ($notifications->isEmpty()) {
                return ApiResponse::success(['notifications' => []], 'No notifications found for this user.');
            }    

            return ApiResponse::success(['notifications'=> $notifications],'Notifications fetched successfully' );

        }catch(\Throwable $e ){

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
        
        try{
            $doctor = Doctor::find($request->doctor_id);
            $patient = Patient::find($request->patient_id);

            if (! $doctor || ! $patient) {
                return ApiResponse::error(null, 'Doctor or patient not found', 404);
            }

           $booking = Booking::create([
                'doctor_id' => $request->doctor_id,
                'patient_id' => $request->patient_id,
                'date_time' => $request->date_time,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'price' => $request->price,
            ]);

            $booking->load(['doctor.user', 'patient.user']);

            // Notify Patient and Doctor 
            event(new BookingCreated($booking));

            return ApiResponse::success([],'Booking created successfully, Then Notify  Patient and Doctor successfully' );

        }catch(\Throwable $e ){
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
       try{
            $notification = Notification::find($id);

            if (! $notification) {
                return ApiResponse::error(null, 'Notification not found', 404);
            }

            $notification->update(['is_read' => true]);

            return ApiResponse::success(null, 'Notification marked as read', 200);

        }catch(\Throwable $e ){
            Log::error('NotificationController@markAsRead failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            return ApiResponse::error(null, 'An unexpected error occurred while marked notifications.', 500);
        }

    }

    public function destroy($id){
        try{
            $notification = Notification::find($id);
            if (! $notification) {
                return ApiResponse::error(null, 'Notification not found.', 404);
            }
            $notification->delete();


            return ApiResponse::success(null, 'Notification deleted successfully');

        }catch(\Throwable $e ){
            Log::error('NotificationController@destroy failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
            ]);
            return ApiResponse::error(null, 'An unexpected error occurred while deleted notifications.', 500);
        }
    }

}
