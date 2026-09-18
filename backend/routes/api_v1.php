<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Client\ClientController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('AuthController.register')->middleware('throttle:10,1');
    Route::post('/login', [AuthController::class, 'login'])->name('AuthController.login')->middleware('throttle:5,1');
});

Route::prefix('auth')->middleware(['jwt.verify'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('AuthController.logout');
});

Route::prefix('clients')->middleware(['jwt.verify'])->group(function () {
    Route::post('/list', [ClientController::class, 'list'])->name('ClientController.list');
    Route::post('/get', [ClientController::class, 'get'])->name('ClientController.get');
    Route::post('/add', [ClientController::class, 'add'])->name('ClientController.add');
    Route::post('/edit', [ClientController::class, 'edit'])->name('ClientController.edit');
    Route::post('/delete', [ClientController::class, 'delete'])->name('ClientController.delete');
});
