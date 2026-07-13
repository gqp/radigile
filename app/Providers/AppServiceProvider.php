<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ForcePasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Validation\Rules\Password;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Pagination\Paginator;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Mail\VerifyEmailMail;
use App\Mail\ResetPasswordMail;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        // Register middleware aliases
        Route::aliasMiddleware('force.password.reset', ForcePasswordReset::class);

        // Baseline password complexity: 8+ chars, upper + lower case, at least one number and one symbol.
        Password::defaults(function () {
            return Password::min(8)->mixedCase()->numbers()->symbols();
        });

        // Brute-force protection: 5 login attempts per minute, keyed by email+IP so one
        // attacker can't lock out a legitimate user, but can't hammer a single account either.
        RateLimiter::for('login', function ($request) {
            $key = Str::lower((string) $request->input('email')) . '|' . $request->ip();
            return Limit::perMinute(5)->by($key);
        });

        // This app ships Bootstrap, not Tailwind — Laravel's default pagination
        // view relies on Tailwind classes that don't apply here, leaving the
        // prev/next arrow SVGs unstyled at native (huge) size.
        Paginator::useBootstrapFive();

        // Route the two built-in auth notifications through our branded
        // Mailables instead of Laravel's plain default template, so every
        // email the app sends looks consistent.
        VerifyEmail::toMailUsing(function ($notifiable, $verificationUrl) {
            return (new VerifyEmailMail($notifiable->name, $verificationUrl))
                ->to($notifiable->getEmailForVerification());
        });

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new ResetPasswordMail($notifiable->name, $url))
                ->to($notifiable->getEmailForPasswordReset());
        });
    }

}
