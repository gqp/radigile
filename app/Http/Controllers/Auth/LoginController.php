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
    /**
     * Where to redirect users after login, based on their role.
     */
    protected function redirectTo()
    {
        return auth()->user()->hasRole('Admin')
            ? route('admin.dashboard')
            : (auth()->user()->hasRole('User') ? route('user.dashboard') : '/');
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
        if (auth()->attempt($request->only(['email', 'password']))) {
            \Log::info('User logged in successfully:', [
                'user_id' => auth()->id(),
                'roles' => auth()->user()->getRoleNames(),
            ]);

            $roleRedirect = $this->redirectTo();
            return redirect($roleRedirect)->with('success', 'Welcome back!');
        }

        // Failed authentication
        \Log::error('Login attempt failed:', ['email' => $request->email]);
        return back()->withErrors(['email' => 'Invalid credentials provided.']);
    }

    public function logout(Request $request)
    {
        // Get user ID before logging out
        $userId = auth()->id();

        // Log the user out
        auth()->logout();

        // Invalidate the current session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clean up session table
        if (!is_null($userId)) {
            \DB::table('sessions')->where('user_id', $userId)->delete();
        }

        \Log::info('User logged out successfully:', ['user_id' => $userId]);

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }


}
