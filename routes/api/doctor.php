<?php

use App\Http\Controllers\Api\DoctorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Doctor API Routes
|--------------------------------------------------------------------------
|
| Routes for doctor operations - requires 'doctor' role
|
*/

Route::middleware(['auth:sanctum', 'role:doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->controller(DoctorController::class)
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', 'dashboard')->name('dashboard');
        Route::get('/doctor/{id}',  'showDoctor')->name('show');

        // Bookings Management
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', 'bookings')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::put('/{id}/confirm', 'confirmBooking')->name('confirm');

        });
    });


