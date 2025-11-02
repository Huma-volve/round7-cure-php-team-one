<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\SendOtpMail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Validation\Rules\Password;
// correct for email verification
// class AuthController extends Controller
// {
//     public function register(RegisterRequest $request)
//     {
//         $profile_photo = null;


//         if ($request->hasFile('profile_photo')) {

//             $path = $request->file('profile_photo')->store('users', 'public');
//             $profile_photo = 'storage/' . $path;
//         }
//         // $otp = rand(10000, 99999);
//         $user = User::create([
//             'name' => $request->name,
//             'email' => $request->email,
//             'mobile' => $request->mobile,
//             'password' => Hash::make($request->password),
//             'birthdate' => $request->birthdate,
//             'gender' => $request->gender,
//             'email_verified_at' => null,
//             // 'email_otp' => $otp,
//             'email_otp' => 1234,
//             'email_otp_expires_at' => now()->addMinutes(5),
//             'location_lat' => $request->location_lat,
//             'location_lng' => $request->location_lng,
//             'profile_photo' => $profile_photo,
//         ]);
//         $user->assignRole('patient');

//         $patient = $user->patient()->create([
//             'medical_notes' => $request->medical_notes,
//             'birthdate' => $request->birthdate,
//             'gender' => $request->gender,

//         ]);


//         try {

//             // Mail::to($user->email)->send(new SendOtpMail($otp));


//             $token = $user->createToken('auth_token')->plainTextToken;
//             return response()->json([
//                 'status' => true,
//                 'message' => 'User registered successfully. Please verify your email with the OTP sent.',
//                 'data' => new UserResource($user),
//                 'token' => $token
//             ]);
//         } catch (Exception $e) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Could not register or send email',
//                 'error' => $e->getMessage()
//             ], 500);
//         }
//     }

//     public function verifyEmailOtp(Request $request)
//     {
//         $request->validate([
//             'email' => 'required|email',
//             'otp' => 'required|digits:4',
//         ]);

//         $user = User::where('email', $request->email)->first();

//         if (!$user) {
//             return response()->json(['status' => false, 'message' => 'User not found'], 404);
//         }

//         if ($user->email_otp !== $request->otp || now()->isAfter($user->email_otp_expires_at)) {
//             return response()->json(['status' => false, 'message' => 'Invalid or expired OTP'], 400);
//         }

//         $user->update([
//             'email_verified_at' => now(),
//             'email_otp' => null,
//             'email_otp_expires_at' => null,
//         ]);

//         $token = $user->createToken('auth_token')->plainTextToken;

//         return response()->json([
//             'status' => true,
//             'message' => 'Email verified successfully.',
//             'token' => $token,
//             'user' => new UserResource($user)
//         ]);
//     }
//     public function login(Request $request)
//     {
//         $request->validate([
//             'email' => ['required', 'email'],
//             'password' => ['required'],
//         ]);

//         $credentials = $request->only('email', 'password');


//         if (Auth::attempt($credentials)) {
//             $user = Auth::user();
//             $token = $user->createToken('auth_token')->plainTextToken;
//             return response()->json([
//                 'status' => true,
//                 'message' => 'Login successful',
//                 'token' => $token,
//                 'user' => new UserResource($user)
//             ]);
//         }

//         return response()->json(['error' => 'fail to login'], 401);
//     }
//     public function logout(Request $request)
//     {
//         $request->user()->currentAccessToken()->delete();
//         return response()->json(['successfully' => 'you have successfully logged out']);
//     }
//     public function resendEmailOtp(Request $request)
//     {
//         $request->validate([
//             'email' => 'required|email',
//         ]);

//         $user = User::where('email', $request->email)->first();

//         if (!$user) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'User not found',
//             ], 404);
//         }

//         if ($user->email_verified_at) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Email already verified',
//             ], 400);
//         }

//         if ($user->email_otp_sent_at && $user->email_otp_sent_at->diffInSeconds(now()) < 60) {
//             return response()->json([
//                 'status' => false,
//                 'message' => 'Please wait 1 minute before requesting a new OTP.'
//             ], 429);
//         }


//         // $otp = rand(10000, 99999);

//         $user->update([
//             // 'email_otp' => $otp,
//             'email_otp' => 1234,
//             'email_otp_expires_at' => now()->addMinutes(5),
//             'email_otp_sent_at' => now(),
//         ]);

//         // Mail::to($user->email)->send(new SendOtpMail($otp));

//         return response()->json([
//             'status' => true,
//             'message' => 'A new OTP has been sent to your email.'
//         ]);
//     }
//     public function sendResetOtp(Request $request)
//     {
//         $request->validate([
//             'email' => 'required|email',
//         ]);

//         $user = User::where('email', $request->email)->first();

//         if (!$user) {
//             return response()->json(['status' => false, 'message' => 'User not found'], 404);
//         }


//         if ($user->email_otp_sent_at && now()->diffInSeconds($user->email_otp_sent_at) < 60) {
//             return response()->json(['status' => false, 'message' => 'Please wait 1 minute before requesting another OTP.'], 429);
//         }

//         $otp = rand(10000, 99999);

//         $user->update([
//             // 'email_otp' => $otp,
//             'email_otp' => 1234,
//             'email_otp_expires_at' => now()->addMinutes(5),
//             'email_otp_sent_at' => now(),
//         ]);

//         // Mail::to($user->email)->send(new SendOtpMail($otp));

//         return response()->json([
//             'status' => true,
//             'message' => 'OTP sent to your email. Please check your inbox.'
//         ]);
//     }
//     public function verifyResetOtp(Request $request)
//     {
//         $request->validate([
//             'email' => 'required|email',
//             'otp' => 'required|digits:5',
//         ]);

//         $user = User::where('email', $request->email)->first();

//         if (!$user) {
//             return response()->json(['status' => false, 'message' => 'User not found'], 404);
//         }

//         if ($user->email_otp !== $request->otp || now()->isAfter($user->email_otp_expires_at)) {
//             return response()->json(['status' => false, 'message' => 'Invalid or expired OTP'], 400);
//         }

//         return response()->json([
//             'status' => true,
//             'message' => 'OTP verified successfully. You can now reset your password.'
//         ]);
//     }
//     public function resetPassword(Request $request)
//     {
//         $request->validate([
//             'email' => 'required|email',
//             'otp' => 'required|digits:4',
//             'password' => 'required|string|min:8',
//         ]);

//         $user = User::where('email', $request->email)->first();

//         if (!$user) {
//             return response()->json(['status' => false, 'message' => 'User not found'], 404);
//         }

//         if ($user->email_otp !== $request->otp || now()->isAfter($user->email_otp_expires_at)) {
//             return response()->json(['status' => false, 'message' => 'Invalid or expired OTP'], 400);
//         }

//         $user->update([
//             'password' => Hash::make($request->password),
//             'email_otp' => null,
//             'email_otp_expires_at' => null,
//             'email_otp_sent_at' => null,
//         ]);

//         return response()->json([
//             'status' => true,
//             'message' => 'Password reset successfully.'
//         ]);
//     }
// }



class AuthController extends Controller
{

    // login
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => [
                'required',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['token' => $token, new UserResource($user)]);
        }

        return response()->json(['error' => 'fail to login'], 401);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['successfully' => 'you have successfully logged out']);
    }
    public function register(RegisterRequest $request)
    {
        $profile_photo = null;


        if ($request->hasFile('profile_photo')) {

            $path = $request->file('profile_photo')->store('users', 'public');
            $profile_photo = 'storage/' . $path;
        }
        // $otp = rand(1000, 9999);
        $otp = 1234;
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'password' => Hash::make($request->password),
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,
            'email_verified_at' => null,
            'email_otp' => $otp,
            // 'email_otp' => 1234,
            'email_otp_expires_at' => now()->addMinutes(3),
            'location_lat' => $request->location_lat,
            'location_lng' => $request->location_lng,
            'profile_photo' => $profile_photo,
        ]);

        $user->assignRole('patient');
        $patient = $user->patient()->create([
            'medical_notes' => $request->medical_notes,
            'birthdate' => $request->birthdate,
            'gender' => $request->gender,

        ]);


        try {


            \App\Services\VonageService::sendSms('+201029737809', "Your OTP code is: $otp");

            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json([
                'status' => true,
                'message' => 'User registered successfully. Please verify your email with the OTP sent.',
                'data' => new UserResource($user),
                'token' => $token
            ]);
        } catch (\Exception $e) {
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

        if ($user->email_otp_sent_at && $user->email_otp_sent_at->diffInSeconds(now()) < 180) {
            return response()->json([
                'status' => false,
                'message' => 'Please wait 3 minute before requesting a new OTP.'
            ], 429);
        }


        // $otp = rand(1000, 9999);
        $otp = 1234;

        $user->update([
            'email_otp' => $otp,
            // 'email_otp' => 1234,
            'email_otp_expires_at' => now()->addMinutes(3),
            'email_otp_sent_at' => now(),
        ]);

        // Mail::to($user->email)->send(new SendOtpMail($otp));
        // \App\Services\VonageService::sendSms($user->mobile, "Your OTP code is: $otp");
        \App\Services\VonageService::sendSms('+201029737809', "Your OTP code is: $otp");


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


        if ($user->email_otp_sent_at && now()->diffInSeconds($user->email_otp_expires_at) < 30) {
            return response()->json(['status' => false, 'message' => 'Please wait 1 minute before requesting another OTP.'], 429);
        }

        // $otp = rand(1000, 9999);
        $otp = 1234;
        $user->update([
            'email_otp' => $otp,
            // 'email_otp' => 1234,
            'email_otp_expires_at' => now()->addMinutes(3),
            'email_otp_sent_at' => now(),
        ]);

        // Mail::to($user->email)->send(new SendOtpMail($otp));
        \App\Services\VonageService::sendSms('+201029737809', "Your OTP code is: $otp");

        return response()->json([
            'status' => true,
            'message' => 'OTP sent to your phone. Please check your inbox.'
        ]);
    }
    public function verifyResetOtp(Request $request)
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

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully. You can now reset your password.'
        ]);
    }
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        // if ($user->email_otp !== $request->otp || now()->isAfter($user->email_otp_expires_at)) {
        //     return response()->json(['status' => false, 'message' => 'Invalid or expired OTP'], 400);
        // }

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
    // login with phone number
    public function sendOtpFormobileLogin(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^01[0-2,5]{1}[0-9]{8}$/',
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json(['error' => 'Mobile not found'], 404);
        }

        // $otp = rand(1000, 9999);
        $otp = 1234;


        $user->update([
            'phone_otp' => $otp,
            'phone_otp_expires_at' => Carbon::now()->addMinutes(3),
        ]);


        // \App\Services\VonageService::sendSms($user->mobile, "Your OTP code is: $otp");

        return response()->json(['message' => 'OTP sent successfully']);
    }


    public function verifyOtpForMobileLogin(Request $request)
    {
        $request->validate([
            'mobile' => 'required|regex:/^01[0-2,5]{1}[0-9]{8}$/',
            'otp' => 'required|numeric',
        ]);

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json(['error' => 'Mobile not found'], 404);
        }


        if (
            $user->phone_otp != $request->otp ||
            Carbon::now()->greaterThan($user->phone_otp_expires_at)
        ) {
            return response()->json(['error' => 'Invalid or expired OTP'], 401);
        }


        $user->update([
            'phone_otp' => null,
            'phone_otp_expires_at' => null,
        ]);


        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => new UserResource($user),
        ]);
    }
    public function deleteAccount(Request $request)
    {
        $user = auth('sanctum')->user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Soft delete
        $user->delete();


        $user->tokens()->delete();

        return response()->json([
            'message' => 'Account deleted successfully ',
        ]);
    }

    public function googleLogin(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $client = new Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($request->token);

        if (!$payload) {
            return response()->json(['error' => 'Invalid Google token'], 401);
        }

        $user = User::updateOrCreate(
            ['email' => $payload['email']],
            [
                'name' => $payload['name'] ?? '',
                'google_id' => $payload['sub'],
                'email_verified_at' => now(),
                'profile_photo' => $payload['picture'] ?? null,
            ]
        );

        Auth::login($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful with Google',
            'token' => $token,
            'user' => $user,
        ]);
    }
}
