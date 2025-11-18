<?php


use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('lang/{locale}', function ($locale) {

       session()->put('locale', $locale);
        app()->setLocale($locale);
    // dd(app()->getLocale());
    return  redirect()->back();
})->name('change.language');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/admin/doctor.php';
require __DIR__.'/auth.php';
require __DIR__.'/admin/web.php';
