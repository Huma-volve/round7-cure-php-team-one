<?php

use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use PhpParser\Comment\Doc;
use Spatie\Permission\Contracts\Role;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/toggle-favorite/{doctorId}', [HomeController::class, 'toggleFavorite'])->name('toggle.favorite');

Route::get('/doctors-details/{id}', [DoctorController::class, 'show'])->name('doctors.show');








Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// روت اختبار RBAC
Route::get('/test-role', function () {
    $user = User::first();

    if (!$user) {
        return response()->json(['error' => 'No users found'], 404);
    }

    return response()->json([
        'user_id' => $user->id,
        'roles' => $user->getRoleNames(),
        'has_admin' => $user->hasRole('admin'),
    ]);
});

// Admin protected routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return response()->json(['ok' => true, 'area' => 'admin only']);
    });
});
