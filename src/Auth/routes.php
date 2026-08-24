<?php

use Illuminate\Support\Facades\Route;
use Therajatspace\Larakit\Auth\Http\Controllers\LoginController;
use Therajatspace\Larakit\Auth\Http\Controllers\RegistrationController;
use Therajatspace\Larakit\Auth\Http\Controllers\ForgotPasswordController;
use Therajatspace\Larakit\Auth\Http\Controllers\ResetPasswordController;
use Therajatspace\Larakit\Auth\Http\Controllers\EmailVerificationController;
use Therajatspace\Larakit\Auth\Http\Controllers\EmailVerificationNotificationController;
use Therajatspace\Larakit\Auth\Http\Controllers\PasswordConfirmationController;

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

    Route::post(
        '/forgot-password',
        [ForgotPasswordController::class, 'store']
    )->middleware('guest')
        ->name('larakit.password.email');

    Route::post(
        '/reset-password',
        [ResetPasswordController::class, 'store']
    )->middleware('guest')
        ->name('larakit.password.update');

    Route::get(
        '/email/verify/{id}/{hash}',
        [EmailVerificationController::class, 'verify']
    )->middleware('auth')
        ->name('larakit.verification.verify');

    Route::post(
        '/email/verification-notification',
        [EmailVerificationNotificationController::class, 'store']
    )->middleware('auth')
        ->name('larakit.verification.send');

    Route::post(
        '/password/confirm',
        [PasswordConfirmationController::class, 'store']
    )->middleware('auth')
        ->name('larakit.password.confirm');

});