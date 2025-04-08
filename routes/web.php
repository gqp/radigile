<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\InviteController;
use App\Http\Controllers\Admin\AdminNotifyController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home Page Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

//About Page Routes
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Notify Me Route
Route::post('/notify-me', [NotifyController::class, 'store'])->name('notify.store');

// Admin Routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth','role:Admin']], function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');

    // Invite Routes
    Route::get('/invites', [InviteController::class, 'index'])->name('admin.invites.index');
    Route::post('/invites/toggle-invite-only', [InviteController::class, 'toggleInviteOnly'])->name('admin.invites.toggle');
    Route::post('/invites/create', [InviteController::class, 'store'])->name('admin.invites.create');
    Route::post('/invites/toggle', [InviteController::class, 'toggleInviteOnly'])->name('admin.invites.toggle');
    Route::put('/invites/disable/{id}', [InviteController::class, 'disable'])->name('admin.invites.disable');
    Route::put('/invites/enable/{id}', [InviteController::class, 'enable'])->name('admin.invites.enable');
    Route::put('/invites/update/{id}', [InviteController::class, 'update'])->name('admin.invites.update');


    //Roles Recource Routes
    Route::resource('roles', RoleController::class);

    //Manage Users Route
    Route::get('/manage-users', [UserController::class, 'manage'])->name('admin.users.manage');

    // Create User
    Route::get('/manage-users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/manage-users', [UserController::class, 'store'])->name('admin.users.store');

    // Edit User
    Route::get('/manage-users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/manage-users/{user}', [UserController::class, 'update'])->name('admin.users.update');

    // Delete User
    Route::delete('/manage-users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // Notify Me - Admin Page Route
    Route::get('/admin/notify-me', [AdminNotifyController::class, 'index'])->name('admin.notify-me');

    // Notify Me - Toggle On & Off
    Route::post('/admin/notify-me/toggle/{id}', [AdminNotifyController::class, 'toggle'])->name('admin.notify-me.toggle');

});

// User Routes
Route::group(['prefix' => 'user', 'middleware' => ['auth','role:User']], function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
});

Auth::routes();
