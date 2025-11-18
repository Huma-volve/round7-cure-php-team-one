<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ContactInfoController;
use App\Http\Controllers\Api\FaqController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| Routes that don't require authentication
|
*/

Route::get('/test-role', function () {
    $user = User::first();
    
    if (!$user) {
        return response()->json(['error' => 'No users found'], 404);
    }
    
    return response()->json([
        'user_id' => $user->id,
        'roles' => $user->getRoleNames(),
        'has_admin' => $user->hasRole('admin'),
        'roles_list' => ['admin', 'doctor', 'patient'],
    ]);
});

Route::get('/faqs', [FaqController::class, 'index'])->name('public.faqs.index');
Route::get('/faqs/{faq}', [FaqController::class, 'show'])->name('public.faqs.show');
Route::post('/contact', [ContactController::class, 'store'])->name('public.contact.store');
Route::get('/contact-info', [ContactInfoController::class, 'show'])->name('public.contact.info');

