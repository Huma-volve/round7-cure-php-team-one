<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $profile_photo = null;


        if ($request->hasFile('profile_photo')) {

            $path = $request->file('profile_photo')->store('users', 'public');
            $profile_photo = 'storage/' . $path;
        }
        // $otp = rand(10000, 99999);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'email_verified_at' => null,
            // 'email_otp' => $otp,
            'email_otp' => 1234,
            'email_otp_expires_at' => now()->addMinutes(5),
            'location_lat' => $request->location_lat,
            'location_lng' => $request->location_lng,
            'profile_photo' => $profile_photo,
        ]);


        $patient = $user->patient()->create([
            'medical_notes' => $request->medical_notes,
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,

        ]);


        try {

            // Mail::to($user->email)->send(new SendOtpMail($otp));


            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'User registered successfully. Please verify your email with the OTP sent.',
                'data' => new UserResource($user),
                'token' => $token
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not register or send email',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function verifyEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:4',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        if ($user->email_otp !== $request->otp || now()->isAfter($user->email_otp_expires_at)) {
            return response()->json(['status' => false, 'message' => 'Invalid or expired OTP'], 400);
        }

        $user->update([
            'email_verified_at' => now(),
            'email_otp' => null,
            'email_otp_expires_at' => null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => true,
            'message' => 'Email verified successfully.',
            'token' => $token,
            'user' => new UserResource($user)
        ]);
    }
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');


        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'Login successful',
                'token' => $token,
                'user' => new UserResource($user)
            ]);
        }

        return response()->json(['error' => 'fail to login'], 401);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['successfully' => 'you have successfully logged out']);
    }
    public function resendEmailOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
            ], 404);
        }

        if ($user->email_verified_at) {
            return response()->json([
                'status' => false,
                'message' => 'Email already verified',
            ], 400);
        }

        if ($user->email_otp_sent_at && $user->email_otp_sent_at->diffInSeconds(now()) < 60) {
            return response()->json([
                'status' => false,
                'message' => 'Please wait 1 minute before requesting a new OTP.'
            ], 429);
        }


        // $otp = rand(10000, 99999);

        $user->update([
            // 'email_otp' => $otp,
            'email_otp' => 1234,
            'email_otp_expires_at' => now()->addMinutes(5),
            'email_otp_sent_at' => now(),
        ]);

        // Mail::to($user->email)->send(new SendOtpMail($otp));

        return response()->json([
            'status' => true,
            'message' => 'A new OTP has been sent to your email.'
        ]);
    }
    public function sendResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }


        if ($user->email_otp_sent_at && now()->diffInSeconds($user->email_otp_sent_at) < 60) {
            return response()->json(['status' => false, 'message' => 'Please wait 1 minute before requesting another OTP.'], 429);
        }

        $otp = rand(10000, 99999);

        $user->update([
            // 'email_otp' => $otp,
            'email_otp' => 1234,
            'email_otp_expires_at' => now()->addMinutes(5),
            'email_otp_sent_at' => now(),
        ]);

        // Mail::to($user->email)->send(new SendOtpMail($otp));

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your email. Please check your inbox.'
        ]);
    }
    public function verifyResetOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:5',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        if ($user->email_otp !== $request->otp || now()->isAfter($user->email_otp_expires_at)) {
            return response()->json(['status' => false, 'message' => 'Invalid or expired OTP'], 400);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully. You can now reset your password.'
        ]);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:4',
            'password' => 'required|string|min:8',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        if ($user->email_otp !== $request->otp || now()->isAfter($user->email_otp_expires_at)) {
            return response()->json(['status' => false, 'message' => 'Invalid or expired OTP'], 400);
        }

        $user->update([
            'password' => Hash::make($request->password),
            'email_otp' => null,
            'email_otp_expires_at' => null,
            'email_otp_sent_at' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Password reset successfully.'
        ]);
    }
}

