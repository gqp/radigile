<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /**
     * Display the password reset form.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function showPasswordResetForm(Request $request)
    {
        $token = $request->route('token'); // Get token from the route
        $email = $request->email; // Get email if provided

        return view('auth.passwords.reset', compact('token', 'email'));
    }

    /**
     * Process the password reset and handle email verification.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processPasswordReset(Request $request)
    {
        // Validate request
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Find the user
        $user = User::where('email', $request->email)->firstOrFail();

        // Update password and clear the reset flag
        $user->update([
            'password' => Hash::make($request->password),
            'force_password_reset' => false, // Remove force reset
        ]);

        // Verify email if not already verified
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // Redirect to login with success message
        return redirect()->route('login')->with('success', 'Your password has been updated, and your email is now verified. Please log in to continue.');
    }
}
