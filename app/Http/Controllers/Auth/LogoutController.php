<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Handle the user logout process.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Debugging: Output the request data to verify what's being sent
        dd(request()->all());


        // Fire logout event automatically in Laravel (optional: attach custom listeners)
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();

        // Regenerate CSRF token
        $request->session()->regenerateToken();

        // Redirect to homepage or a custom route
        return redirect()->route('homepage')->with('status', 'You have been logged out successfully.');
    }
}
