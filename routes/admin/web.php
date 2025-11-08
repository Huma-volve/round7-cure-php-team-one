<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\DisputeController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\SettingController;



/*
|--------------------------------------------------------------------------
| Admin Web Routes (Panel Views)
|--------------------------------------------------------------------------
|
| Web routes for the admin panel interface
| These routes serve the Blade views for the admin dashboard
|
*/

Route::middleware(['auth', 'verified', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Users Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{id}/roles', [UserController::class, 'updateRoles'])->name('users.updateRoles');

        // Bookings Management
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
        Route::get('/bookings/{id}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
        Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
        Route::post('/bookings/{id}/status', [BookingController::class, 'updateStatus'])->name('bookings.updateStatus');
        Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');

        // Payments Monitoring
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{id}/refund', [PaymentController::class, 'refund'])->name('payments.refund');

        // Disputes Management
        Route::get('/disputes', [DisputeController::class, 'index'])->name('disputes.index');
        Route::get('/disputes/{type}/{id}', [DisputeController::class, 'show'])->name('disputes.show');
        Route::post('/disputes/{type}/{id}/resolve', [DisputeController::class, 'resolve'])->name('disputes.resolve');
        Route::post('/disputes/{type}/{id}/notes', [DisputeController::class, 'addNote'])->name('disputes.addNote');

        // Support Tickets
        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');

        // Doctors Management
        Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
        Route::get('/doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
        Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
        Route::get('/doctors/{id}', [DoctorController::class, 'show'])->name('doctors.show');
        Route::get('/doctors/{id}/edit', [DoctorController::class, 'edit'])->name('doctors.edit');
        Route::put('/doctors/{id}', [DoctorController::class, 'update'])->name('doctors.update');
        Route::delete('/doctors/{id}', [DoctorController::class, 'destroy'])->name('doctors.destroy');
        Route::post('/doctors/{id}/toggle-status', [DoctorController::class, 'toggleStatus'])->name('doctors.toggleStatus');

        // Patients Management
        Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
        Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
        Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
        Route::get('/patients/{id}', [PatientController::class, 'show'])->name('patients.show');
        Route::get('/patients/{id}/edit', [PatientController::class, 'edit'])->name('patients.edit');
        Route::put('/patients/{id}', [PatientController::class, 'update'])->name('patients.update');
        Route::delete('/patients/{id}', [PatientController::class, 'destroy'])->name('patients.destroy');

 // Settings
             Route::get('/settings' , [SettingController::class , 'index'])->name('settings.index');
             Route::get('/settings' , [SettingController::class , 'update']);
    });

