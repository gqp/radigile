<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Middleware\CheckAccountLocked;

// Homepage Route
Route::get('/', [HomeController::class, 'index'])->name('homepage');

// Test Email Route
Route::get('test-email', function () {
    try {
        Mail::raw('This is a test email', function ($message) {
            $message->to('gqplaisted@gmail.com')->subject('Radigile - Test Email');
        });

        return 'Email sent!';
    } catch (\Throwable $e) {
        return 'Email failed to send: ' . $e->getMessage();
    }
});

// Login, Registration, and Forgot Password Routes (Guest Only)
Route::middleware(['web', 'guest', CheckAccountLocked::class])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetPasswordLink'])->name('password.email');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/dashboard', function () { return view('dashboard');})->name('dashboard');

});

// Dashboard and Logout Routes (Authenticated Users Only)
Route::middleware(['web', 'guest', CheckAccountLocked::class])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetPasswordLink'])->name('password.email');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');
