<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        // Check if the "remember me" option is selected
        $remember = $request->has('remember');

        // Attempt to authenticate the user
        if (Auth::attempt($request->only('email', 'password'), $remember)) {
            // Regenerate the session to prevent session fixation
            $request->session()->regenerate();

            // Redirect to the intended page with a success message
            return redirect()->intended(route('dashboard'))->with('status', 'Welcome back!');
        }

        // Handle invalid login attempt, return with error and preserve the email input
        return back()->withErrors([
            'email' => __('Invalid email or password. Please try again.'),
        ])->onlyInput('email');
    }
}
