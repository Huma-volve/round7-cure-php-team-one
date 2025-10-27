<?php

use App\Events\MessageSent;
use App\Http\Controllers\ChatController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

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

Route::post('/login', function (Request $request) {
    $user = User::where('email', $request->email)->first();

    // if (! $user || ! Hash::check($request->password, $user->password)) {
        // return response()->json(['message' => 'Invalid credentials'], 401);
    // }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
    ]);
});

 



// ==========start Routs chat=============
/**Packagist
 *composer require laravel/reverb
 *php artisan reverb:install
 */
Route::apiResource('chats', ChatController::class);
