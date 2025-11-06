<?php

use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\DoctorBookingController;
use App\Http\Controllers\Api\DoctorPaymentController;
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
        ->get('/doctor/patients/search',
        [DoctorController::class, 'searchDoctorPatients'])->name('doctor.patients.search');

    Route::middleware(['auth:sanctum', 'role:doctor'])
        ->get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');

    Route::middleware(['auth:sanctum', 'role:doctor'])
    ->get('/doctor/patient/{patientId}',
    [DoctorController::class, 'showPatient'])->name('patient.show');

    Route::middleware(['auth:sanctum'])
    ->get('/doctor/earnings', [DoctorController::class , 'earnings'])->name('doctor.earnings');

    Route::middleware(['auth:sanctum', 'role:patient'])
    ->get('/doctor/{id}', [DoctorController::class, 'showDoctor'])->name('doctor.show');

Route::middleware(['auth:sanctum', 'role:doctor'])
    ->prefix('doctor')
    ->name('doctor.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DoctorController::class, 'dashboard'])->name('dashboard');

        // Bookings Management
        Route::prefix('bookings')->name('bookings.')
            ->controller(DoctorBookingController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}', 'show')->name('show');
                Route::put('/{id}/confirm', 'confirm')->name('confirm');
                Route::put('/{id}/cancel', 'cancel')->name('cancel');
                Route::put('/{id}/reschedule', 'reschedule')->name('reschedule');
            });

        // Payments Management
        Route::prefix('payments')->name('payments.')
            ->controller(DoctorPaymentController::class)
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/booking/{bookingId}', 'getBookingPayment')->name('booking');
                Route::get('/stats', 'getStats')->name('stats');
            });
    });
