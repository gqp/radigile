<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\InviteController;

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

    // Invite Routes
    Route::get('/invites', [InviteController::class, 'index'])->name('admin.invites.index');
    Route::post('/invites/create', [InviteController::class, 'store'])->name('admin.invites.create');
    Route::post('/invites/toggle', [InviteController::class, 'toggleInviteOnly'])->name('admin.invites.toggle');
    Route::put('/invites/disable/{id}', [InviteController::class, 'disable'])->name('admin.invites.disable');

    //Roles Recource Routes
    Route::resource('roles', RoleController::class);

    //Manage Users Route
    Route::get('/manage-users', [UserController::class, 'manage'])->name('admin.users.index');

});

// User Routes
Route::group(['prefix' => 'user', 'middleware' => ['auth','role:User']], function () {
    Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('user.profile');
    Route::get('/invites', [InviteController::class, 'userIndex'])->name('user.invites');
});

Auth::routes();
