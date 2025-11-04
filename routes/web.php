<?php

use App\Http\Controllers\Dashboard\BackupController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Settings
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'update'])->name('settings.update');

    // Backups
    Route::get('backups', [BackupController::class, 'index'])->name('backups.index');
    Route::post('backups/create', [BackupController::class, 'create'])->name('backups.create');
});


require __DIR__.'/admin/doctor.php';
