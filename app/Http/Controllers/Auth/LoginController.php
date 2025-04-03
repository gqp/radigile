<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @return string
     */
    protected function redirectTo()
    {
        if (auth()->user()->hasRole('Admin')) {
            return route('admin.dashboard');
        }

        if (auth()->user()->hasRole('User')) {
            return route('user.dashboard');
        }

        // Default fallback in case a user role doesn't match Admin/User
        return '/';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Attempt authentication
        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            // Redirect based on the user's role
            if (auth()->user()->hasRole('Admin')) {
                return redirect()->route('admin.dashboard');
            }

            if (auth()->user()->hasRole('User')) {
                return redirect()->route('user.dashboard');
            }

            // Default fallback
            return redirect('/');
        }

        // If authentication fails, redirect back with an error
        return redirect()->route('login')->with('error', 'Invalid email or password.');
    }
}
