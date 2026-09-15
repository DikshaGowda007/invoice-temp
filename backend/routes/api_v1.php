<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('AuthController.register')->middleware('throttle:10,1');
    Route::post('/login', [AuthController::class, 'login'])->name('AuthController.login')->middleware('throttle:5,1');
});

Route::prefix('auth')->middleware(['jwt.verify'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('AuthController.logout');
});
