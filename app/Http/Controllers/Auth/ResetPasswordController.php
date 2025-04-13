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
    public function showForcePasswordResetForm()
    {
        // Render a simple form for users to set a new password
        return view('auth.passwords.force-reset');
    }

    public function processForcePasswordReset(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = $request->user(); // Get the authenticated user

        // Update the password
        $user->update([
            'password' => Hash::make($request->password),
            'force_password_reset' => false, // Remove force reset flag
        ]);

        return redirect()->route('home')->with('success', 'Your password has been updated successfully.');
    }
}
