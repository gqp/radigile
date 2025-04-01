<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\RoleManagementController;
use App\Http\Middleware\CheckAccountLocked;
use App\Http\Middleware\CheckRole;

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

// Routes for Guests Only (Unauthenticated Users)
Route::middleware(['web', 'guest', CheckAccountLocked::class])->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetPasswordLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

// Routes for Authenticated Users (General Access)
Route::middleware(['auth', 'web', CheckAccountLocked::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Unified logout route for all users
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');
});

// Admin-Specific Routes (Role-Based Access)
Route::middleware(['auth', CheckRole::class . ':admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::put('/update-role/{user}', [RoleManagementController::class, 'updateRole'])->name('admin.updateRole');
});

// Shared Admin and Editor Routes (Role: admin, editor)
Route::middleware(['auth', CheckRole::class . ':admin,editor'])->prefix('admin')->group(function () {
    Route::get('/content', function () {
        return view('admin.content');
    })->name('admin.content');
});
