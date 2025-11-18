<?php

use App\Http\Controllers\Doctor\BookingController;
use App\Http\Controllers\Doctor\DashboardController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\PaymentController;
use App\Http\Controllers\Doctor\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'role:doctor'])
    ->prefix('admin/doctor')
    ->name('doctor.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

        Route::get('/schedule', [ScheduleController::class, 'edit'])->name('schedule.edit');
        Route::post('/schedule', [ScheduleController::class, 'update'])->name('schedule.update');
    });