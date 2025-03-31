<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Rate limiting key based on the user's email and IP
        $throttleKey = $this->throttleKey($request);

        // Check if the user is currently locked out
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            // Throw a validation exception to inform the user of the lockout
            throw ValidationException::withMessages([
                'email' => __("Too many login attempts. Please try again in :seconds seconds.", ['seconds' => $seconds]),
            ]);
        }

        // Attempt to authenticate the user
        if (Auth::attempt($request->only('email', 'password'), $request->has('remember'))) {
            // Clear any failed attempts in the rate limiter for this user
            RateLimiter::clear($throttleKey);

            // Regenerate the session to prevent session fixation
            $request->session()->regenerate();

            // Redirect to the intended page with a success message
            return redirect()->intended(route('dashboard'))->with('status', 'Welcome back!');
        }

        // Increment the failed login attempts for the throttle key
        RateLimiter::hit($throttleKey, 60 * 15); // Lockout period of 15 minutes

        // Handle invalid login attempt, return with error and preserve the email input
        throw ValidationException::withMessages([
            'email' => __('Invalid email or password. Please try again.'),
        ])->onlyInput('email');
    }

    /**
     * Generate a unique throttle key for the request.
     *
     * @param Request $request
     * @return string
     */
    protected function throttleKey(Request $request)
    {
        return strtolower($request->input('email')) . '|' . $request->ip();
    }
}
