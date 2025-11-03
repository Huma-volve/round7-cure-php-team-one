<?php

use App\Http\Controllers\Dashboard\doctor\DoctorController;
use Illuminate\Support\Facades\Route;


Route::prefix('doctor')
->name('doctor.')
->controller(DoctorController::class)
->group(function () {

Route::get('/','index')->name('index');

});




