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
use Google_Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    // login
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => [
                'required'
            ],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if (!$user->hasAnyRole(['patient', 'doctor'])) {
                Auth::logout();
                return response()->json(['error' => 'user not allowed to login '], 403);
            }
            $token = $user->createToken('auth_token')->plainTextToken;
            return response()->json(['token' => $token, new UserResource($user)]);
        }

        return response()->json(['error' => 'Incorrect email or password'], 401);
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

        ]);


        try {



            \App\Services\VonageService::sendSms('+201029737809', "Your OTP code is: $otp");

            // Mail::to($user->email)->send(new SendOtpMail($otp));



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

        //\App\Services\VonageService::sendSms('+201029737809', "Your OTP code is: $otp");



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



        if ($user->email_otp_sent_at && $user->email_otp_sent_at->diffInSeconds(now()) < 180) {
            return response()->json(['status' => false, 'message' => 'Please wait 3 minute before requesting another OTP.'], 429);
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

    public function getGoogleAuthUrl(Request $request): JsonResponse
    {
        $state = $request->input('state', bin2hex(random_bytes(16)));

        $client = $this->buildGoogleClient($state);

        return response()->json([
            'success' => true,
            'data' => [
                'url' => $client->createAuthUrl(),
                'state' => $state,
            ],
        ]);
    }

    public function handleGoogleCallback(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'state' => 'nullable|string',
        ]);

        try {
            $client = $this->buildGoogleClient($request->input('state'));
            $tokenData = $client->fetchAccessTokenWithAuthCode($request->code);
        } catch (\Exception $e) {
            // Check if request wants JSON response (API call) or redirect (web browser)
            if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to exchange authorization code with Google.',
                'error' => $e->getMessage(),
            ], 500);
            }
            
            // Redirect to frontend with error
            $frontendUrl = env('FRONTEND_URL', env('APP_URL'));
            return redirect($frontendUrl . '/auth/google/callback?error=' . urlencode('Unable to exchange authorization code'));
        }

        if (isset($tokenData['error'])) {
            if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $tokenData['error_description'] ?? 'Failed to fetch Google tokens.',
            ], 400);
            }
            
            $frontendUrl = env('FRONTEND_URL', env('APP_URL'));
            return redirect($frontendUrl . '/auth/google/callback?error=' . urlencode($tokenData['error_description'] ?? 'Failed to fetch Google tokens'));
        }

        $idToken = $tokenData['id_token'] ?? null;

        if (!$idToken) {
            if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Google response did not include an ID token.',
            ], 400);
            }
            
            $frontendUrl = env('FRONTEND_URL', env('APP_URL'));
            return redirect($frontendUrl . '/auth/google/callback?error=' . urlencode('Google response did not include an ID token'));
        }

        $payload = $client->verifyIdToken($idToken);

        if (!$payload) {
            if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to verify Google ID token.',
            ], 401);
        }

            $frontendUrl = env('FRONTEND_URL', env('APP_URL'));
            return redirect($frontendUrl . '/auth/google/callback?error=' . urlencode('Unable to verify Google ID token'));
        }

        // Complete login using existing method (web guard for callback)
        $loginResponse = $this->completeGoogleLogin($payload, 'web');
        
        // If request wants JSON (API call), return JSON directly
        if ($request->wantsJson() || $request->expectsJson()) {
            return $loginResponse;
        }

        // Extract token and user from response
        $responseData = json_decode($loginResponse->getContent(), true);
        $token = $responseData['token'] ?? null;
        
        if (!$token) {
            $frontendUrl = env('FRONTEND_URL', env('APP_URL'));
            return redirect($frontendUrl . '/auth/google/callback?error=' . urlencode('Failed to generate authentication token'));
        }

        // For web browser, redirect to frontend with token
        $frontendUrl = env('FRONTEND_URL', env('APP_URL'));
        $redirectUrl = $frontendUrl . '/auth/google/callback?token=' . urlencode($token) . '&success=true';
        
        // For mobile apps, check if state contains mobile app identifier
        $state = $request->input('state', '');
        if (str_contains($state, 'mobile://') || str_contains($state, 'app://')) {
            // Mobile app deep link
            return redirect($state . '?token=' . urlencode($token) . '&success=true');
        }
        
        return redirect($redirectUrl);
    }

    public function googleLogin(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        // Strict validation: Google ID tokens MUST be in JWT format (3 parts separated by dots)
        $token = trim($request->token);
        $tokenParts = explode('.', $token);
        $isJWT = count($tokenParts) === 3;

        // Reject Sanctum tokens (format: id|token or just token part)
        if (str_contains($token, '|')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token type',
                'error' => 'The provided token appears to be a Laravel Sanctum token, not a Google ID token.',
                'hint' => 'Google ID tokens must be in JWT format (3 parts separated by dots, starting with eyJ...). Get the token from Google Identity Services on your mobile app, not from Laravel authentication.',
                'received_token_preview' => substr($token, 0, 50) . '...',
                'token_length' => strlen($token),
            ], 400);
        }

        // STRICT: Reject any token that is not JWT format (must have exactly 3 parts)
        if (!$isJWT) {
            $tokenLength = strlen($token);
            $startsWithEyJ = str_starts_with($token, 'eyJ');
            
            return response()->json([
                'success' => false,
                'message' => 'Invalid token format - not a JWT',
                'error' => 'Google ID tokens must be in JWT format with exactly 3 parts separated by dots.',
                'hint' => 'The token you sent has ' . count($tokenParts) . ' part(s), but Google ID tokens must have exactly 3 parts (header.payload.signature). Make sure you are sending the Google ID token from Google Sign-In SDK, not a Sanctum token or authorization code.',
                'token_format' => [
                    'parts_count' => count($tokenParts),
                    'starts_with_eyJ' => $startsWithEyJ,
                    'length' => $tokenLength,
                    'preview' => substr($token, 0, 50) . '...',
                ],
                'expected_format' => 'eyJ... (3 parts separated by dots)',
            ], 400);
        }

        // Additional validation: JWT tokens should start with 'eyJ' (base64 encoded JSON header)
        $startsWithEyJ = str_starts_with($token, 'eyJ');
        if (!$startsWithEyJ) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid JWT format',
                'error' => 'Google ID tokens (JWT) must start with "eyJ" (base64 encoded JSON header).',
                'hint' => 'The token format looks incorrect. Make sure you are sending the complete Google ID token from Google Sign-In SDK.',
                'token_preview' => substr($token, 0, 50) . '...',
            ], 400);
        }

        // Validate token length (JWT tokens are typically 500+ characters)
        if (strlen($token) < 100) {
            return response()->json([
                'success' => false,
                'message' => 'Token too short',
                'error' => 'Google ID tokens are typically 500+ characters long. The provided token is too short.',
                'hint' => 'Make sure you are sending the complete Google ID token, not a partial token or Sanctum token.',
                'token_length' => strlen($token),
            ], 400);
        }

        try {
            $clientId = env('GOOGLE_CLIENT_ID');
            
            if (!$clientId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Google Client ID not configured',
                    'error' => 'GOOGLE_CLIENT_ID is not set in .env file.',
                    'hint' => 'Please set GOOGLE_CLIENT_ID in your .env file.',
                ], 500);
            }

            $client = new Google_Client(['client_id' => $clientId]);
            $payload = $client->verifyIdToken($token);

        if (!$payload) {
                // Try to decode token to get more info
                $tokenParts = explode('.', $token);
                $decodedPayload = null;
                
                if (count($tokenParts) >= 2) {
                    try {
                        $decodedPayload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $tokenParts[1])), true);
                    } catch (\Exception $e) {
                        // Ignore decode errors
                    }
                }

                $errorDetails = [
                    'success' => false,
                    'message' => 'Invalid Google token',
                    'error' => 'Unable to verify Google ID token. The token may be expired, invalid, or the Client ID does not match.',
                    'hint' => 'Make sure you are sending a fresh Google ID token from Google Sign-In SDK, and that the Client ID in your .env matches the one used to generate the token.',
                ];

                if ($decodedPayload) {
                    $errorDetails['token_info'] = [
                        'audience' => $decodedPayload['aud'] ?? null,
                        'issuer' => $decodedPayload['iss'] ?? null,
                        'expires_at' => isset($decodedPayload['exp']) ? date('Y-m-d H:i:s', $decodedPayload['exp']) : null,
                        'is_expired' => isset($decodedPayload['exp']) ? (time() > $decodedPayload['exp']) : null,
                        'configured_client_id' => $clientId,
                        'client_id_match' => isset($decodedPayload['aud']) && $decodedPayload['aud'] === $clientId,
                    ];
                }

                return response()->json($errorDetails, 401);
        }

            return $this->completeGoogleLogin($payload, 'api');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify Google token',
                'error' => $e->getMessage(),
                'hint' => 'Make sure you are sending a valid Google ID token (JWT format) from Google Sign-In SDK. Check that GOOGLE_CLIENT_ID in .env matches the Client ID used to generate the token.',
                'exception_type' => get_class($e),
            ], 400);
        }
    }

    protected function buildGoogleClient(?string $state = null): Google_Client
    {
        $client = new Google_Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));

        if ($secret = env('GOOGLE_CLIENT_SECRET')) {
            $client->setClientSecret($secret);
        }

        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI', config('services.google.redirect')));
        $client->setScopes(['email', 'profile']);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        if ($state !== null) {
            $client->setState($state);
        }

        return $client;
    }

    protected function completeGoogleLogin(array $payload, string $guard = 'api'): JsonResponse
    {
        $user = User::updateOrCreate(
            ['email' => $payload['email']],
            [
                'name' => $payload['name'] ?? '',
                'google_id' => $payload['sub'],
                'mobile' => isset($payload['phoneNumber']) ? $payload['phoneNumber'] : substr('google-' . $payload['sub'], 0, 20),
                'password' => isset($payload['email']) && ($existing = User::where('email', $payload['email'])->first())
                    ? $existing->password
                    : Hash::make(Str::random(32)),
                'email_verified_at' => now(),
                'profile_photo' => $payload['picture'] ?? null,
            ]
        );

        // Ensure google_id is updated even if user already exists
        if ($user->google_id !== $payload['sub']) {
            $user->google_id = $payload['sub'];
            $user->save();
        }

        // Assign patient role with the specified guard
        if (!$user->hasRole('patient', $guard)) {
            $user->assignRole('patient', $guard);
        }

        // Login with the specified guard
        if ($guard === 'web') {
            Auth::guard('web')->login($user);
        }
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful with Google',
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Get Google user data from ID token or authorization code
     */
    public function getGoogleUserData(Request $request): JsonResponse
    {
        $request->validate([
            'token' => 'required|string',
            'code' => 'nullable|string', // If code is provided, it's an authorization code
        ]);

        try {
            $client = $this->buildGoogleClient();
            $payload = null;

            // Check if it's a Google ID token (JWT format: 3 parts separated by dots)
            $tokenParts = explode('.', $request->token);
            $isJWT = count($tokenParts) === 3;

            // Reject Laravel Sanctum tokens (format: id|token or just token part)
            if (str_contains($request->token, '|')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid token type',
                    'error' => 'The provided token appears to be a Laravel Sanctum token, not a Google ID token.',
                    'hint' => 'Google ID tokens must be in JWT format (3 parts separated by dots: eyJ...). Get the token from Google Identity Services on frontend, not from Laravel authentication. If you want to get Google data for authenticated user, use GET /api/google/my-data instead.',
                ], 400);
            }

            // Reject tokens that look like Sanctum token parts
            // Google ID tokens start with 'eyJ' (base64 encoded JWT header)
            // Authorization codes are usually longer and may start with '4/'
            // Sanctum token parts are shorter alphanumeric strings
            if (!$isJWT) {
                $tokenLength = strlen($request->token);
                $startsWithEyJ = str_starts_with($request->token, 'eyJ');
                $startsWith4Slash = str_starts_with($request->token, '4/');
                
                // If it's short (< 50 chars) and doesn't look like authorization code, reject it
                if ($tokenLength < 50 && !$startsWith4Slash && !$startsWithEyJ) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid token format',
                        'error' => 'The provided token does not appear to be a valid Google ID token or authorization code.',
                        'hint' => 'Google ID tokens must be in JWT format (3 parts separated by dots, starting with eyJ...). Authorization codes are longer strings usually starting with "4/". If you want to get Google data for authenticated user, use GET /api/google/my-data with your Sanctum token in Authorization header.',
                    ], 400);
                }
            }

            // Check if it's an authorization code (exchange for tokens)
            if ($request->has('code') || !$isJWT) {
                // It's an authorization code, exchange it for tokens
                try {
                    $tokenData = $client->fetchAccessTokenWithAuthCode($request->code ?? $request->token);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to exchange authorization code',
                        'error' => $e->getMessage(),
                        'hint' => 'The provided token is not a valid Google ID token (JWT) or authorization code. Google ID tokens must be in JWT format (3 parts separated by dots: eyJ...). Authorization codes expire quickly (usually within minutes).',
                    ], 400);
                }
                
                if (isset($tokenData['error'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to exchange authorization code',
                        'error' => $tokenData['error_description'] ?? $tokenData['error'],
                        'hint' => 'The authorization code may be expired or invalid. Please get a new code from Google OAuth flow.',
                    ], 400);
                }

                $idToken = $tokenData['id_token'] ?? null;
                
                if (!$idToken) {
                    return response()->json([
                        'success' => false,
                        'message' => 'ID token not found in response',
                        'token_data' => $tokenData, // Show what we got
                    ], 400);
                }

                // Verify the ID token
                $payload = $client->verifyIdToken($idToken);
            } else {
                // It's an ID token, verify it directly
                $payload = $client->verifyIdToken($request->token);
            }

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid Google token',
                ], 401);
            }

            // Extract available data from Google payload
            return response()->json([
                'success' => true,
                'data' => [
                    'email' => $payload['email'] ?? null,
                    'name' => $payload['name'] ?? null,
                    'given_name' => $payload['given_name'] ?? null,
                    'family_name' => $payload['family_name'] ?? null,
                    'picture' => $payload['picture'] ?? null,
                    'google_id' => $payload['sub'] ?? null,
                    'email_verified' => $payload['email_verified'] ?? false,
                    'locale' => $payload['locale'] ?? null,
                    'full_payload' => $payload, // All available data from Google
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify Google token',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Google user data for authenticated user
     * Returns Google data stored in user profile
     */
    public function getMyGoogleData(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'email' => $user->email,
                'name' => $user->name,
                'picture' => $user->profile_photo,
                'google_id' => $user->google_id,
                'email_verified_at' => $user->email_verified_at,
                'profile_photo' => $user->profile_photo,
                'mobile' => $user->mobile,
                'has_google_account' => !empty($user->google_id),
            ],
        ]);
    }
}
