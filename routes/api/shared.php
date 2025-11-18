<?php

use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\PatientPaymentMethodController;
use App\Http\Controllers\Api\SupportTicketController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\WebhookController;

/*
|--------------------------------------------------------------------------
| Shared API Routes
|--------------------------------------------------------------------------
|
| Routes accessible by authenticated users (both patients and doctors)
|
*/

Route::middleware(['auth:sanctum'])
    ->prefix('doctors')
    ->name('doctors.')
    ->controller(DoctorController::class)
    ->group(function () {
        Route::get('/{doctorId}/available-slots', 'getAvailableSlots')
            ->name('available-slots');
    });

// Payments
Route::middleware(['auth:sanctum'])
    ->prefix('payments')
    ->name('payments.')
    ->controller(PaymentController::class)
    ->group(function () {
        Route::post('/create-intent', 'createIntent')->name('create-intent');
        Route::post('/confirm', 'confirm')->name('confirm');
        Route::get('/{payment}', 'show')->name('show');
    });

// Patient payment methods
Route::middleware(['auth:sanctum'])
    ->prefix('payment-methods')
    ->name('payment-methods.')
    ->controller(PatientPaymentMethodController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'store')->name('store');
        Route::put('/{paymentMethod}/default', 'setDefault')->name('set-default');
        Route::delete('/{paymentMethod}', 'destroy')->name('destroy');
    });

// Support tickets
Route::middleware(['auth:sanctum'])
    ->prefix('support/tickets')
    ->name('support.tickets.')
    ->controller(SupportTicketController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{ticket}', 'show')->name('show');
        Route::post('/', 'store')->name('store');
        Route::post('/{ticket}/reply', 'reply')->name('reply');
    });

// Webhooks (public by provider, protect via secrets)
Route::post('/webhooks/stripe', [WebhookController::class, 'stripe'])->name('webhooks.stripe');
Route::post('/webhooks/paypal', [WebhookController::class, 'paypal'])->name('webhooks.paypal');

