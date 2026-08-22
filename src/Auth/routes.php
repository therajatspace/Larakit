<?php

use Illuminate\Support\Facades\Route;
use Therajatspace\Larakit\Auth\Http\Controllers\LoginController;
use Therajatspace\Larakit\Auth\Http\Controllers\RegistrationController;

Route::middleware('web')->group(function () {
    Route::post(
        '/register',
        [RegistrationController::class, 'store']
    )->name('larakit.register');

    Route::post(
        '/login',
        [LoginController::class, 'store']
    )->name('larakit.login');

    Route::post(
        '/logout',
        [LoginController::class, 'destroy']
    )->name('larakit.logout');
});