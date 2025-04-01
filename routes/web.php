<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\Admin\RoleManagementController; // For role management
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

Route::middleware(['auth', 'web', CheckAccountLocked::class])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard'); // Redirect admins to their dashboard
        }

        return view('dashboard'); // Regular users see this view
    })->name('dashboard');
});

// admin Routes Group for Role-based Access
Route::middleware(['auth', CheckRole::class . ':admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Role Management route
    Route::put('/update-role/{user}', [RoleManagementController::class, 'updateRole'])->name('admin.updateRole');
});

// Routes for admin and Editor (Shared Access)
Route::middleware(['auth', CheckRole::class . ':admin,editor'])->prefix('admin')->group(function () {
    Route::get('/content', function () {
        return view('admin.content');
    })->name('admin.content');
});

// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
    ->middleware('signed')
    ->name('verification.verify');

// Authenticated Users Only
Route::middleware(['auth', 'web', CheckAccountLocked::class])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

});

// Admin Logout (using role-based middleware)
Route::middleware(['auth', CheckRole::class . ':admin'])->prefix('admin')->group(function () {
    // Admin dashboard and logout route
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::post('/logout', [LogoutController::class, 'logout'])->name('admin.logout');
});

