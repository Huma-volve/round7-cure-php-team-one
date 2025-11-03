<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BookingController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PaymentDisputeController;
use App\Http\Controllers\Admin\BookingDisputeController;
use App\Http\Controllers\Admin\TicketController;

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
        
        // Users
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');

        // Bookings
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');

        // Payments (monitoring)
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show');

        // Disputes
        Route::get('/disputes/payments', [PaymentDisputeController::class, 'index'])->name('disputes.payments.index');
        Route::get('/disputes/payments/{id}', [PaymentDisputeController::class, 'show'])->name('disputes.payments.show');
        Route::get('/disputes/bookings', [BookingDisputeController::class, 'index'])->name('disputes.bookings.index');
        Route::get('/disputes/bookings/{id}', [BookingDisputeController::class, 'show'])->name('disputes.bookings.show');

        // Tickets
        Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
        Route::get('/tickets/{id}', [TicketController::class, 'show'])->name('tickets.show');
    });

