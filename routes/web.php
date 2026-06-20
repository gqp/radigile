<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AdminNotifyController;
use App\Http\Controllers\Admin\AdminTeamController;
use App\Http\Controllers\Admin\AdminTeamMemberController;
use App\Http\Controllers\Admin\InviteController;
use App\Http\Controllers\Admin\QuestionCategoryController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\TeamDomainController;
use App\Http\Controllers\Admin\TeamFrameworksController;
use App\Http\Controllers\Admin\TeamInvitationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotifyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserTeamController;
use App\Http\Controllers\UserTeamMemberController;
use App\Http\Middleware\CheckActiveStatus;
use App\Http\Middleware\ForcePasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\StripeWebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication and Email Verification Routes
Auth::routes(['verify' => true]);

// Password Reset Routes
Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset'); // Handles the password reset form
Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update'); // Handles form submission to reset the password

// ----------------- Public Routes -----------------
Route::middleware(['web'])->group(function () {
    // Home Page Routes
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // About Page Routes
    Route::get('/about', [AboutController::class, 'index'])->name('about');

    // Notify Me Route
    Route::post('/notify-me', [NotifyController::class, 'store'])->name('notify.store');

    // Add the accept team invitation route here
    Route::get('/team/invitation/accept', [InviteController::class, 'accept'])
        ->name('team.invitation.accept');
});

// ----------------- Authenticated User Routes -----------------
Route::middleware(['web', 'auth', ForcePasswordReset::class])->group(function () {
    Route::get('/password/force-reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showForcePasswordResetForm'])
        ->name('password.force.reset');

    Route::post('/password/force-reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'processForcePasswordReset'])
        ->name('password.force.reset.process');
});


// ----------------- Admin Routes -----------------
Route::group(['prefix' => 'admin', 'middleware' => ['web', 'auth', 'role:Admin', CheckActiveStatus::class, ForcePasswordReset::class]], function () {
    // ---- Dashboard and Admin Profile----
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::get('/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::get('/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::put('/admin/update-profile', [AdminController::class, 'updateProfile'])->name('admin.updateProfile');
    Route::put('/admin/update-name', [AdminController::class, 'updateName'])->name('admin.updateName');
    Route::put('/admin/update-password', [AdminController::class, 'updatePassword'])->name('admin.updatePassword');

    // ---- Invite Management Routes ----
    Route::get('/invites', [InviteController::class, 'index'])->name('admin.invites.index');
    Route::post('/invites/create', [InviteController::class, 'store'])->name('admin.invites.create');
    Route::post('/invites/toggle', [InviteController::class, 'toggleInviteOnly'])->name('admin.invites.toggle');
    Route::put('/invites/disable/{id}', [InviteController::class, 'disable'])->name('admin.invites.disable');
    Route::put('/invites/enable/{id}', [InviteController::class, 'enable'])->name('admin.invites.enable');
    Route::put('/invites/update/{id}', [InviteController::class, 'update'])->name('admin.invites.update');
    Route::post('/invites/registered', [InviteController::class, 'createForRegisteredUser'])->name('admin.invites.registered');

    // ---- Roles Resource Routes ----
    Route::resource('roles', RoleController::class)->names([
        'index' => 'admin.roles.index',
        'create' => 'admin.roles.create',
        'store' => 'admin.roles.store',
        'edit' => 'admin.roles.edit',
        'update' => 'admin.roles.update',
        'destroy' => 'admin.roles.destroy',
    ]);

    // ---- User Management ----
    Route::get('/manage-users', [UserController::class, 'manage'])->name('admin.users.index');
    Route::get('/manage-users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/manage-users', [UserController::class, 'store'])->name('admin.users.store');
    Route::patch('/admin/users/{id}/toggle-active', [UserController::class, 'toggleActive'])->name('admin.users.toggle-active');
    Route::get('/manage-users/{user}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::put('/manage-users/{user}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/manage-users/{user}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    // ---- Admin Team Management ----
    Route::resource('/teams', AdminTeamController::class)->names([
        'index' => 'admin.teams.index',      // View all teams
        'create' => 'admin.teams.create',   // Create new team
        'store' => 'admin.teams.store',     // Store team
        'edit' => 'admin.teams.edit',       // Edit team
        'update' => 'admin.teams.update',   // Update team
        'destroy' => 'admin.teams.destroy', // Delete team
        'show' => 'admin.teams.show',       // Show specific team
    ]);

    Route::resource('team-domains', TeamDomainController::class)->names([
        'index' => 'admin.team-domains.index',
        'create' => 'admin.team-domains.create',
        'store' => 'admin.team-domains.store',
        'edit' => 'admin.team-domains.edit',
        'update' => 'admin.team-domains.update',
        'destroy' => 'admin.team-domains.destroy',
    ]);

    Route::resource('team-frameworks', TeamFrameworksController::class)->names([
        'index' => 'admin.team-frameworks.index',
        'create' => 'admin.team-frameworks.create',
        'store' => 'admin.team-frameworks.store',
        'edit' => 'admin.team-frameworks.edit',
        'update' => 'admin.team-frameworks.update',
        'destroy' => 'admin.team-frameworks.destroy',
    ]);

    // Admin Team Members Management
    Route::group(['prefix' => 'teams/{team}/members', 'as' => 'admin.teams.members.'], function () {
        Route::get('/', [AdminTeamMemberController::class, 'index'])->name('index'); // List members of a team
        Route::get('/create', [AdminTeamMemberController::class, 'create'])->name('create'); // Add new member
        Route::post('/', [AdminTeamMemberController::class, 'store'])->name('store'); // Store member
        Route::get('/{member}/edit', [AdminTeamMemberController::class, 'edit'])->name('edit'); // Edit member
        Route::put('/{member}', [AdminTeamMemberController::class, 'update'])->name('update'); // Update member
        Route::delete('/{member}', [AdminTeamMemberController::class, 'destroy'])->name('destroy'); // Remove member
    });

    // ---- Question Management Routes ----
    Route::resource('questions', QuestionController::class)->names([
        'index' => 'admin.questions.index',
        'create' => 'admin.questions.create',
        'store' => 'admin.questions.store',
        'edit' => 'admin.questions.edit',
        'update' => 'admin.questions.update',
        'destroy' => 'admin.questions.destroy',
    ]);

    Route::resource('question-categories', QuestionCategoryController::class)->names([
        'index' => 'admin.question-categories.index',
        'create' => 'admin.question-categories.create',
        'store' => 'admin.question-categories.store',
        'edit' => 'admin.question-categories.edit',
        'update' => 'admin.question-categories.update',
        'destroy' => 'admin.question-categories.destroy',
    ]);

    Route::resource('tags', TagController::class)->names([
        'index' => 'admin.tags.index',
        'create' => 'admin.tags.create',
        'store' => 'admin.tags.store',
        'edit' => 'admin.tags.edit',
        'update' => 'admin.tags.update',
        'destroy' => 'admin.tags.destroy',
    ]);

    // ---- Admin Notify Me Routes ----
    Route::get('/admin/notify-me', [AdminNotifyController::class, 'index'])->name('admin.notify-me');
    Route::post('/notify-me/toggle-global', [AdminNotifyController::class, 'toggleGlobal'])->name('admin.notify-me.toggle-global');
    Route::post('/notify-me/{id}/send-invite', [AdminNotifyController::class, 'sendInvite'])->name('admin.notify.send-invite');

    // ---- Subscription Management Routes ----
    Route::get('/subscriptions/plans', [SubscriptionController::class, 'indexPlans'])->name('admin.plans.index');
    Route::get('/subscriptions/plans/create', [SubscriptionController::class, 'createPlan'])->name('admin.plans.create');
    Route::post('/subscriptions/plans', [SubscriptionController::class, 'storePlan'])->name('admin.plans.store');
    Route::get('/subscriptions/', [SubscriptionController::class, 'indexSubscriptions'])->name('admin.subscriptions.index');
    Route::get('/subscriptions/create', [SubscriptionController::class, 'createSubscription'])->name('admin.subscriptions.create');
    Route::post('/subscriptions/store', [SubscriptionController::class, 'storeSubscription'])->name('admin.subscriptions.store');
    Route::get('/subscriptions/{subscription}/edit', [SubscriptionController::class, 'editSubscription'])->name('admin.subscriptions.edit');
    Route::put('/subscriptions/{subscription}', [SubscriptionController::class, 'updateSubscription'])->name('admin.subscriptions.update');
    Route::get('/subscriptions/plans/{plan}/edit', [SubscriptionController::class, 'editPlan'])->name('admin.plans.edit'); // Show edit form
    Route::put('/subscriptions/plans/{plan}', [SubscriptionController::class, 'updatePlan'])->name('admin.plans.update'); // Handle form submission
    Route::delete('/subscriptions/plans/{plan}', [SubscriptionController::class, 'destroyPlan'])->name('admin.plans.destroy');
    Route::delete('/subscriptions/{subscription}', [SubscriptionController::class, 'destroySubscription'])->name('admin.subscriptions.destroy');

});

// ----------------- User Routes -----------------
Route::prefix('user')
    ->middleware(['web', 'auth', 'verified', 'role:User', CheckActiveStatus::class])
    ->group(function () {

        // ---- User Dashboard ----
        Route::get('/dashboard', [UserController::class, 'index'])->name('user.dashboard');

        // ---- User Team Management ----
        Route::resource('/teams', UserTeamController::class)->names([
            'index' => 'user.teams.index',      // View all teams
            'create' => 'user.teams.create',   // Create new team
            'store' => 'user.teams.store',     // Store team
            'edit' => 'user.teams.edit',       // Edit team
            'update' => 'user.teams.update',   // Update team
            'destroy' => 'user.teams.destroy', // Delete team
            'show' => 'user.teams.show',       // Show specific team
        ]);

        Route::get('/teams/invite/accept/{code}', [UserTeamMemberController::class, 'acceptInvite'])
            ->name('user.teams.invite.accept')
            ->middleware(['auth']);

        // ---- User Profile ----
        Route::prefix('profile')->group(function () {
            Route::get('/', [UserController::class, 'profile'])->name('user.profile'); // View profile
            Route::put('/update-name', [UserController::class, 'updateName'])->name('user.updateName'); // Update name
            Route::put('/update-password', [UserController::class, 'updatePassword'])->name('user.updatePassword'); // Update password
            Route::put('/update-profile', [AdminController::class, 'updateProfile'])->name('user.updateProfile'); // Update full profile
        });

        // ---- User Subscription ----
        Route::post('/subscribe/free', [SubscriptionController::class, 'subscribeToFreePlan'])->name('subscribe.free');
    });

// ----------------- Misc Subscription Routes -----------------
Route::middleware(['web', 'auth', CheckActiveStatus::class])->group(function () {
    Route::get('/access-feature', [SubscriptionController::class, 'accessFeature'])->name('subscription.access-feature');
    Route::get('/check-free-tier', [SubscriptionController::class, 'checkFreeTier'])->name('subscription.check-free-tier');
});

// ----------------- Billing Routes (Stripe) -----------------
Route::middleware(['web', 'auth', 'verified', 'role:User', CheckActiveStatus::class])->prefix('billing')->group(function () {
    Route::get('/checkout/{plan}', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('/success', [BillingController::class, 'success'])->name('billing.success');
    Route::get('/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
    Route::get('/portal', [BillingController::class, 'portal'])->name('billing.portal');
});

// ----------------- Stripe Webhook -----------------
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
