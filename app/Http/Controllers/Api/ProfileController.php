<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{   // update
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $request->validate([
            'name' => ['string', 'max:255'],
            'password' => ['nullable', Password::defaults()],
            'current_password' => ['required_with:password'],
            // 'mobile' => ['nullable', 'min:11', 'numeric', Rule::unique('users', 'mobile')->ignore($user->id)],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,png,jpeg,gif,svg', 'max:2048'],
            'birthdate' => ['nullable', 'date', 'date_format:Y-m-d'],
            'gender' => ['nullable', 'in:male,female'],
            'medical_notes' => ['nullable', 'string'],
        ]);

        $data = [];

        if ($request->filled('name')) $data['name'] = $request->name;
        if ($request->filled('birthdate')) $data['birthdate'] = $request->birthdate;
        if ($request->filled('gender')) $data['gender'] = $request->gender;

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
            $user->patient()->update([
                'birthdate' => $request->birthdate ?? $user->birthdate,
                'gender' => $request->gender ?? $user->gender,
                'medical_notes' => $request->medical_notes ?? optional($user->patient)->medical_notes,
            ]);
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
            'mobile' => 'required|string|max:20',
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
            'new_mobile' => 'required|string|max:20',
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
