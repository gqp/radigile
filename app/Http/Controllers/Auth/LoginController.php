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

        // Clear cache before attempting login
        cache()->flush();


        // Attempt authentication
        if (auth()->attempt(['email' => $request->email, 'password' => $request->password])) {
            // Redirect based on the user's role
            if (auth()->user()->hasRole('Admin')) {
                return redirect()->route('admin.dashboard')
                    ->with('success', 'Welcome Admin!');
            }

            if (auth()->user()->hasRole('User')) {
                return redirect()->route('user.dashboard')
                ->with('success', 'Welcome User!');
            }

            // Default fallback
            return redirect('/');
        }

        // If authentication fails, redirect back with an error
        return redirect()->route('login')->with('error', 'Invalid email or password.');
    }

    public function logout(Request $request)
    {
        // Log the user out
        auth()->logout();

        // Flush the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect to the homepage
        return redirect('/')
            ->with('success', 'You have been logged out successfully.');
    }

}
