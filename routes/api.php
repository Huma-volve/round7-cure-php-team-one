<?php




use App\Models\User;
use PhpParser\Comment\Doc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Contracts\Role;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\Chat\ChatMessageController;
use App\Http\Controllers\Api\Chat\DoctorChatController;
use App\Http\Controllers\Api\Chat\PatientChatController;
use App\Http\Controllers\Api\Chat\MessageController;
use App\Http\Controllers\Api\SpecialtyController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Broadcast;

Broadcast::routes(['middleware' => ['auth:sanctum']]);

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


Route::get('/', [HomeController::class, 'index'])->name('home')->middleware('auth:sanctum');
Route::get('/specialties', [SpecialtyController::class, 'index'])->name('specialties.index');

Route::post('/store-search-history', [SearchController::class, 'storeSearch'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/favorites/toggle/{doctor}', [FavoriteController::class, 'toggleFavorite']);
    Route::get('/favorites', [FavoriteController::class, 'getFavorites']);
     Route::get('/favorites/check/{doctor}', [FavoriteController::class, 'checkFavorite']);

});

Route::apiResource('reviews', ReviewController::class)->middleware('auth:sanctum');
Route::apiResource('notifications', NotificationController::class)->middleware('auth:sanctum');


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
// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verifyEmailOtp', [AuthController::class, 'verifyEmailOtp'])->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/resend-verify-otp', [AuthController::class, 'resendEmailOtp'])->middleware('auth:sanctum');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password/send-otp', [AuthController::class, 'sendResetOtp']);
Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyResetOtp']);
Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword']);
Route::middleware('auth:sanctum')->controller(ProfileController::class)->group(function () {
    Route::post('/mobile/request-change', 'requestMobileChange');
    Route::post('/mobile/verify-change', 'verifyMobileChange');
    Route::put('/updateProfile', 'updateProfile');

});
Route::post('/sendOtpFormobileLogin', [AuthController::class, 'sendOtpFormobileLogin']);
Route::post('/verifyOtpForMobileLogin', [AuthController::class, 'verifyOtpForMobileLogin']);
Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);


/*first case for the patient */
/* when patient be login can see all the doctors to chat with them */
/*he click on button this button to check if it has chat with this doctor before if yes wil get all of the messages
else wil create new row in table chat and return the id and all things
*/
Route::middleware('auth:sanctum')->group(function () {





    Route::get('/chats', [ChatController::class, 'index']);


    Route::get('/chats/{chat}/messages', [MessageController::class, 'index']);

//                                FAIL


    // Route::get('/chat/doctor', [DoctorChatController::class, 'index']);
    // Route::get('/chat/patient', [PatientChatController::class, 'index']);
    // Route::post('/chats/{chat}/messages', [MessageController::class, 'store']);
    // Route::post('/messages/{chat}', [MessageController::class, 'send']); //
    // Route::post('/messages/send', [MessageController::class, 'send']);   //
    // Route::post('/messages/mark-read', [MessageController::class, 'markRead']);
});



/*second case for the doctor */
/* if doctor open the page chat he wel see only message are sending to him  */




// Authentication routes in public.php


// Load route files
require __DIR__ . '/api/public.php';
require __DIR__ . '/api/shared.php';
require __DIR__ . '/api/patient.php';
require __DIR__ . '/api/doctor.php';
require __DIR__ . '/api/admin.php';

