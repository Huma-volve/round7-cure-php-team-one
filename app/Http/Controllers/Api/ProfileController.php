<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\UpdateProfileRequest;
use App\Traits\HandlesRoleUpdates;

class ProfileController extends Controller
{   
    use HandlesRoleUpdates;
    // update
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        

        $data = [];

        foreach (['name', 'birthdate', 'gender'] as $field) {
            if ($request->filled($field)) {
                $data[$field] = $request->$field;
            }
        }

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('users', 'public');
            $data['profile_photo'] = 'storage/' . $path;
        }

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['status' => false, 'message' => 'Current password is incorrect'], 400);
            }
            $data['password'] = Hash::make($request->password);
        }
        
        $user->update($data);

        if ($user->hasRole('patient')) {
            $this->handlePatientUpdate($user, $request);
        }

        if ($user->hasRole('doctor')) {
            $this->handleDoctorUpdate($user, $request);
        }


        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully',
            'data' => new UserResource($user->fresh())
        ]);
    }

    public function requestMobileChange(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^01[0-2,5]{1}[0-9]{8}$/',
        ]);

        $user = auth()->user();
        // $otp = rand(1000, 9999);
        $otp = 1234;


        $user->update([
            'phone_otp' => $otp,
            'phone_otp_expires_at' => now()->addMinutes(3),
        ]);


        \App\Services\VonageService::sendSms('+201029737809', "Your OTP code is: $otp");


        session(['new_mobile' => $request->mobile]);

        return response()->json([
            'message' => 'OTP sent to your new mobile number.'
        ]);
    }

    public function verifyMobileChange(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric',
            'new_mobile' => 'required|regex:/^01[0-2,5]{1}[0-9]{8}$/',
        ]);

        $user = auth()->user();

        if (
            $user->phone_otp == $request->otp &&
            $user->phone_otp_expires_at &&
            now()->lessThan($user->phone_otp_expires_at)
        ) {
            $user->update([
                'mobile' => $request->new_mobile,
                'phone_otp' => null,
                'phone_otp_expires_at' => null,
            ]);

            session()->forget('new_mobile');

            return response()->json([
                'message' => 'Mobile number updated successfully.'
            ]);
        }

        return response()->json([
            'message' => 'Invalid or expired OTP.'
        ], 400);
    }
}
