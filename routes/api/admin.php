<?php

use Illuminate\Support\Facades\Route;

// Admin API Controller
// TODO: Create AdminApiController in app/Http/Controllers/Api/AdminApiController.php

/*
|--------------------------------------------------------------------------
| Admin API Routes
|--------------------------------------------------------------------------
|
| Routes for admin operations - requires 'admin' role
|
*/

Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        Route::get('/dashboard', function () {
            return response()->json([
                'ok' => true,
                'area' => 'admin only',
                'message' => 'Welcome to admin dashboard'
            ]);
        })->name('dashboard');
        
        // Add more admin routes here
        // Route::get('/users', [AdminController::class, 'users'])->name('users');
    });

