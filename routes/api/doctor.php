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
   Route::middleware('auth:sanctum')->prefix('doctors')->name('doctors.')->group(function () {

    //  كل المستخدمين يقدروا يشوفوا قائمة الدكاترة
    Route::get('/', [DoctorController::class, 'index'])->name('index');

    //  تفاصيل دكتور معين (للمرضى فقط)
    Route::get('/{doctor}', [DoctorController::class, 'showDoctor'])
        ->name('show');

    //  الأرباح الخاصة بالدكتور (للدكتور فقط)
    Route::middleware('role:doctor,api')->get('doctor/earnings', [DoctorController::class, 'earnings'])
        ->name('earnings');

    //  بحث داخل مرضى الدكتور (للدكتور فقط)
    Route::middleware('role:doctor,api')->get('/patients/search', [DoctorController::class, 'searchPatients'])
        ->name('patients.search');

    //  عرض مريض محدد عند دكتور معين
    Route::middleware('role:doctor,api')->get('/patients/{patient}', [DoctorController::class, 'showPatient'])
        ->name('patients.show');
});

Route::middleware(['auth:sanctum', 'role:doctor,api'])
    ->prefix('doctor')
    ->name('api.doctor.')
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
