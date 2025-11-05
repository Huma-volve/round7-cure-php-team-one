<?php

use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Patient API Routes
|--------------------------------------------------------------------------
|
| Routes for patient operations - requires 'patient' role
|
*/

Route::middleware(['auth:sanctum', 'role:patient'])
    ->prefix('patient')
    ->name('patient.')
    ->controller(PatientController::class)
    ->group(function () {
        
        // Bookings Management
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::post('/', 'bookAppointment')->name('store');
            Route::get('/', 'myBookings')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::put('/{id}/reschedule', 'reschedule')->name('reschedule');
            Route::delete('/{id}/cancel', 'cancel')->name('cancel');
        });
    });

