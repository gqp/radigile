<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\InviteController;
use App\Http\Controllers\Admin\AdminNotifyController;
use App\Http\Middleware\PreventBackHistoryMiddleware;

// Public Routes
Route::middleware(['web'])->group(function () {
    // Home Page Routes
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // About Page Routes
    Route::get('/about', [AboutController::class, 'index'])->name('about');

    // Notify Me Route
    Route::post('/notify-me', [NotifyController::class, 'store'])->name('notify.store');

    // Login and Logout Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login'); // Show login form
    Route::post('/login', [LoginController::class, 'login'])->name('login.process'); // Process login
});

// Protected Routes with PreventBackHistoryMiddleware
Route::middleware(['web', PreventBackHistoryMiddleware::class, 'auth'])->group(function () {
    // Logout Route
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Admin Routes (Protected with Role Middleware)
    Route::middleware(['role:Admin'])->prefix('admin')->group(function () {
        // Dashboard Route
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');

        // Profile and Settings Routes
        Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
        Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
        Route::put('/admin/update-name', [AdminController::class, 'updateName'])->name('admin.updateName');
        Route::put('/admin/update-password', [AdminController::class, 'updatePassword'])->name('admin.updatePassword');

        // Invite Routes
        Route::get('/invites', [InviteController::class, 'index'])->name('admin.invites.index');
        Route::post('/invites/create', [InviteController::class, 'store'])->name('admin.invites.create');
        Route::post('/invites/toggle', [InviteController::class, 'toggleInviteOnly'])->name('admin.invites.toggle');
        Route::put('/invites/disable/{id}', [InviteController::class, 'disable'])->name('admin.invites.disable');
        Route::put('/invites/enable/{id}', [InviteController::class, 'enable'])->name('admin.invites.enable');
        Route::put('/invites/update/{id}', [InviteController::class, 'update'])->name('admin.invites.update');

        // Role Resource Routes
        Route::resource('roles', RoleController::class)->names([
            'index' => 'admin.roles.index',
            'create' => 'admin.roles.create',
            'store' => 'admin.roles.store',
            'edit' => 'admin.roles.edit',
            'update' => 'admin.roles.update',
            'destroy' => 'admin.roles.destroy',
        ]);

        // Manage Users Routes
        Route::get('/manage-users', [UserController::class, 'manage'])->name('admin.users.index');
        Route::get('/manage-users/create', [UserController::class, 'create'])->name('admin.users.create');
        Route::post('/manage-users', [UserController::class, 'store'])->name('admin.users.store');
        Route::patch('/admin/users/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');

        // Edit User Routes
        Route::get('/manage-users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
        Route::put('/manage-users/{user}', [UserController::class, 'update'])->name('admin.users.update');

        // Delete User
        Route::delete('/manage-users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

        // Notify Me Routes
        Route::get('/admin/notify-me', [AdminNotifyController::class, 'index'])->name('admin.notify-me');
        Route::post('/notify-me/toggle-global', [AdminNotifyController::class, 'toggleGlobal'])->name('admin.notify-me.toggle-global');
        Route::post('notify-me/{id}/send-invite', [AdminNotifyController::class, 'sendInvite'])->name('admin.notify.send-invite');

        // Subscriptions and Plans Routes
        Route::get('/subscriptions/plans', [SubscriptionController::class, 'indexPlans'])->name('admin.plans.index');
        Route::get('/subscriptions/plans/create', [SubscriptionController::class, 'createPlan'])->name('admin.plans.create');
    });
});
