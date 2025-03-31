<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class VerificationController extends Controller
{
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('homepage')
                ->with('status', 'Your email is already verified.');
        }

        $request->fulfill();

        return redirect()->route('login')
            ->with('status', 'Email verified successfully. Please log in.');
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return back()->with('status', 'Email already verified.');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Verification email resent.');
    }
}
