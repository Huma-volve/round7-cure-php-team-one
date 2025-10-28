<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// روت اختبار RBAC
Route::get('/test-role', function () {
    $user = User::first();
    
    if (!$user) {
        return response()->json(['error' => 'No users found'], 404);
    }
    
    return response()->json([
        'user_id' => $user->id,
        'roles' => $user->getRoleNames(),
        'has_admin' => $user->hasRole('admin'),
    ]);
});

// Admin protected routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json(['ok' => true, 'area' => 'admin only']);
    });
});
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verifyEmailOtp', [AuthController::class, 'verifyEmailOtp'])->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/resend-email-otp', [AuthController::class, 'resendEmailOtp'])->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password/send-otp', [AuthController::class, 'sendResetOtp']);
Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyResetOtp']);
Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword']);