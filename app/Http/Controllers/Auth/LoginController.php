<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

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

        // Rate limiting
        $throttleKey = $this->throttleKey($request);
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return redirect()->back()->withErrors([
                'email' => __("Too many login attempts. Please try again in :seconds seconds.", ['seconds' => $seconds]),
            ])->withInput($request->only('email'));
        }

        // Attempt to authenticate
        if (Auth::attempt($request->only('email', 'password'), $request->has('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            $user = Auth::user();

            // Role-based redirection
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('status', 'Welcome back, Admin!');
            }

            // Other roles redirection
            return redirect()->route('dashboard')->with('status', 'Welcome back!');
        }

        // Failed login attempt
        RateLimiter::hit($throttleKey, 60 * 15);
        return redirect()->back()->withErrors([
            'email' => __('Invalid email or password. Please try again.'),
        ])->withInput($request->only('email'));
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
