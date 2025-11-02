<?php


use App\Http\Controllers\Api\ReviewController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\NotificationController;
use PhpParser\Comment\Doc;
use Spatie\Permission\Contracts\Role;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/toggle-favorite/{doctorId}', [HomeController::class, 'toggleFavorite'])->name('toggle.favorite');

Route::get('/doctors-details/{id}', [DoctorController::class, 'showDoctor'])->name('doctors.show');



/*
|--------------------------------------------------------------------------
| API Routes - Cure Platform
|--------------------------------------------------------------------------
|
| All API routes are loaded via route service provider.
| Routes are organized in separate files for better maintainability:
| - routes/api/public.php   -> Public endpoints (no auth required)
| - routes/api/patient.php  -> Patient endpoints (role: patient)
| - routes/api/doctor.php   -> Doctor endpoints (role: doctor)
| - routes/api/admin.php    -> Admin endpoints (role: admin)
| - routes/api/shared.php   -> Shared endpoints (authenticated users)
|
*/



Route::apiResource('reviews',ReviewController::class)->middleware('auth:sanctum');
Route::apiResource('notifications',NotificationController::class)->middleware('auth:sanctum');



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

// Authentication routes in public.php


// Load route files
require __DIR__.'/api/public.php';
require __DIR__.'/api/shared.php';
require __DIR__.'/api/patient.php';
require __DIR__.'/api/doctor.php';
require __DIR__.'/api/admin.php';

