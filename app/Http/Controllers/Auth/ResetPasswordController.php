<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Process the password reset and handle email verification if required.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showPasswordResetForm(Request $request)
    {
        $token = $request->route('token'); // Retrieve token from the route (if applicable)
        $email = $request->email; // Get the email if provided as a query string (optional)

        return view('auth.passwords.reset', compact('token', 'email'));
    }

    public function processPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Fetch the user
        $user = User::where('email', $request->email)->firstOrFail();

        // Update the password
        $user->update([
            'password' => Hash::make($request->password),
            'force_password_reset' => false, // Remove the flag after the password is updated
        ]);

        // Mark email as verified upon successful password reset
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // Redirect to the login page with a success message
        return redirect()->route('login')->with('success', 'Your password has been updated, and your email is now verified. Please log in to continue.');
    }
}
