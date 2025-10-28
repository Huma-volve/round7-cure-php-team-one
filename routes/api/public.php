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

