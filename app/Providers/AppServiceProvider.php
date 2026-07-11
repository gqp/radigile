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
    }

}
