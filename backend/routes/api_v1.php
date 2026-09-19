<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Invoice\InvoiceController;
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

Route::prefix('invoices')->middleware(['jwt.verify'])->group(function () {
    Route::post('/list', [InvoiceController::class, 'list'])->name('InvoiceController.list');
    Route::post('/get', [InvoiceController::class, 'get'])->name('InvoiceController.get');
    Route::post('/add', [InvoiceController::class, 'add'])->name('InvoiceController.add');
    Route::post('/clone', [InvoiceController::class, 'clone'])->name('InvoiceController.clone');
    Route::post('/edit', [InvoiceController::class, 'edit'])->name('InvoiceController.edit');
    Route::post('/delete', [InvoiceController::class, 'delete'])->name('InvoiceController.delete');
    Route::post('/update-status', [InvoiceController::class, 'updateStatus'])->name('InvoiceController.updateStatus');
    Route::post('/line-item/add', [InvoiceController::class, 'addLineItem'])->name('InvoiceController.addLineItem');
    Route::post('/line-item/update', [InvoiceController::class, 'updateLineItem'])->name('InvoiceController.updateLineItem');
    Route::post('/line-item/delete', [InvoiceController::class, 'deleteLineItem'])->name('InvoiceController.deleteLineItem');
});
