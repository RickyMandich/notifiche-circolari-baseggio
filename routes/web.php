<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SubscriberController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/iscritti');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/iscritti', [SubscriberController::class, 'index'])->name('subscribers.index');
    Route::post('/iscritti', [SubscriberController::class, 'store'])->name('subscribers.store');
    Route::delete('/iscritti/{subscriber}', [SubscriberController::class, 'destroy'])->name('subscribers.destroy');
});
