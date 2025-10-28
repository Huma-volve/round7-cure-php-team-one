<?php

use App\Http\Controllers\Api\DoctorController;
use Illuminate\Support\Facades\Route;

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

