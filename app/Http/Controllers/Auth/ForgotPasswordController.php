<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetPasswordLink(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email|exists:users,email', // Ensure email exists
        ]);

        // Attempt to send the reset link
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Handle response
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __('A password reset link has been sent to your email.'))
            : back()->withErrors(['email' => __('Unable to send reset link. Please try again later.')]);
    }
}
