<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| Routes that don't require authentication
|
*/

Route::get('/test-role', function () {
    $user = User::first();
    
    if (!$user) {
        return response()->json(['error' => 'No users found'], 404);
    }
    
    return response()->json([
        'user_id' => $user->id,
        'roles' => $user->getRoleNames(),
        'has_admin' => $user->hasRole('admin'),
        'roles_list' => ['admin', 'doctor', 'patient'],
    ]);
});

// Authentication endpoints
use App\Http\Controllers\Api\AuthController;

Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::get('/user', [AuthController::class, 'user'])->name('user')->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum')->name('logout');

