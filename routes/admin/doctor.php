<?php

use App\Http\Controllers\Dashboard\doctor\DoctorController;
use Illuminate\Support\Facades\Route;


Route::prefix('admin/doctor')
->name('doctor.')
->controller(DoctorController::class)
->group(function () {

Route::get('/','index')->name('index');
Route::get('/patient/{id}', 'show')
    ->name('patients.show');

});




