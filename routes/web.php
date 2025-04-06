<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home Page
Route::get('/', [HomeController::class, 'index'])->name('home');

//About Page
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Admin Routes
Route::group(['prefix' => 'admin', 'middleware' => ['auth','role:Admin']], function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');

    Route::resource('roles', RoleController::class);
});

Route::group(['prefix' => 'admin/invites', 'middleware' => ['auth', 'role:Admin']], function () {
    Route::get('/', [InviteController::class, 'index'])->name('admin.invites.index');
    Route::post('/create', [InviteController::class, 'store'])->name('admin.invites.create');
    Route::post('/toggle', [InviteController::class, 'toggleInviteOnly'])->name('admin.invites.toggle');
});

// User Routes
Route::group(['prefix' => 'user', 'middleware' => ['auth','role:User']], function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
});

Auth::routes();
