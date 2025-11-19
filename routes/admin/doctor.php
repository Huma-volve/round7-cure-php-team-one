<?php

use App\Http\Controllers\Doctor\BookingController;
use App\Http\Controllers\Doctor\DashboardController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\PaymentController;
use App\Http\Controllers\Doctor\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web', 'verified', 'role:doctor,web'])
    ->prefix('admin/doctor')
    ->name('doctor.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{id}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
        Route::post('/bookings/{id}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::post('/bookings/{id}/reschedule', [BookingController::class, 'reschedule'])->name('bookings.reschedule');
        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');

        Route::get('/schedule', [ScheduleController::class, 'edit'])->name('schedule.edit');
        Route::post('/schedule', [ScheduleController::class, 'update'])->name('schedule.update');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
    });