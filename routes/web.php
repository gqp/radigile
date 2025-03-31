<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\DashboardController;
use App\Http\Middleware\CheckAccountLocked;


// Homepage Routes
Route::get('/', [HomeController::class, 'index'])->name('homepage');

//Test Email Routes
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

// Login, Logout and Forgot Password Routes
Route::middleware(['guest', CheckAccountLocked::class])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetPasswordLink'])->name('password.email');
});


// Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard'); // Replace with your dashboard view
    })->name('dashboard');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
});


// Register and Email Verification Routes
Route::group(['middleware' => ['web','auth']], function () {
    // User Registration Routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

// Email Verification Routes
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/verify/resend', [VerificationController::class, 'resend'])->name('verification.resend');

// Registration Routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

// Email Verification Routes
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/verify/resend', [VerificationController::class, 'resend'])->name('verification.resend');

});
