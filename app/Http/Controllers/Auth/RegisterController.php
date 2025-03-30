<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register'); // Create Blade template
    }

    public function register(Request $request)
    {
        $request->validate([
            'fist-name' => 'required|string|max:255',
            'last-name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
            ],
            'invitation_code' => 'required|string|exists:invitations,invitation_code',
            'agreed_to_terms' => 'accepted',
        ]);

        $invitation = Invitation::where('invitation_code', $request->invitation_code)->first();

        if (!$invitation || !$invitation->isValid()) {
            throw ValidationException::withMessages([
                'invitation_code' => 'The invitation code is invalid or expired.',
            ]);
        }

        $user = User::create([
            'first-name' => $request->fisrt-name,
            'last-name' => $request->last-name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'invitation_code' => $request->invitation_code,
            'agreed_to_terms' => true,
        ]);

        $invitation->delete();

        $user->sendEmailVerificationNotification();

        //return back()->with('status', 'Registration successful. Please check your email for verification.');
        return redirect()->route('homepage')->with('success', 'You have registered successfully!');

    }

}
