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
    public function showResetForm(Request $request, $token = null)
    {
        // Check that the token is provided
        if (!$token) {
            abort(404, 'Password reset token is missing.');
        }

        // Pass the token and email to the 'reset password' form view
        return view('auth.passwords.reset', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    public function reset(Request $request)
    {
        // Validate the password reset input
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Attempt to reset the user's password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // Handle success or failure
        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }


    public function showForcePasswordResetForm()
    {
        // Render a simple form for users to set a new password
        return view('auth.passwords.force-reset');
    }

    public function processForcePasswordReset(Request $request)
    {
        \Log::info('Processing forced password reset. Request received.', ['user_id' => optional(auth()->user())->id]);

        // Step 1: Validate input
        $validatedData = $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        \Log::info('Form validation passed.', $validatedData);

        // Step 2: Retrieve the authenticated user
        $user = $request->user();

        if (!$user) {
            \Log::error('No authenticated user found. Password reset failed.');
            return redirect()->route('password.force.reset')->withErrors('Could not process the request. Please try again.');
        }

        // Step 3: Attempt to update the user's password
        try {
            $updateSuccessful = $user->update([
                'password' => Hash::make($request->password),
                'force_password_reset' => false, // Remove force reset flag
            ]);

            if ($updateSuccessful) {
                \Log::info('Password reset successfully for user.', ['user_id' => $user->id]);

                return redirect()->route('user.dashboard')->with('success', 'Your password has been updated successfully.');
            } else {
                \Log::warning('Password reset failed for user.', ['user_id' => $user->id]);
                return redirect()->route('password.force.reset')->withErrors('Failed to update the password. Please try again.');
            }
        } catch (\Exception $e) {
            \Log::error('An error occurred during password reset.', [
                'error' => $e->getMessage(),
                'user_id' => $user->id ?? 'N/A',
            ]);

            return redirect()->route('password.force.reset')->withErrors('An unexpected error occurred.');
        }
    }
}
