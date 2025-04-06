<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Invite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class UserInviteController extends Controller
{
    /**
     * Display the invite form.
     */
    public function index()
    {
        $user = Auth::user(); // Get authenticated user

        // Fetch the active invite for the user, if exists
        $invite = Invite::where('created_by', $user->id)
            ->where('is_active', true)
            ->first();

        if (!$invite) {
            return redirect()->back()->withErrors(['No active invite found.']);
        }

        return view('dashboard.user.invite', [
            'remainingInvites' => $user->remaining_invites, // Pass remaining invites
            'inviteCode' => $invite->code, // Pass user's invite code
        ]);
    }
    /**
     * Handle invite sending.
     */
    public function sendInvites(Request $request)
    {
        $user = Auth::user();

        // Validate the input email(s)
        $request->validate([
            'emails.*' => 'required|email',
            'invite_code' => 'required|exists:invites,code',
        ]);

        // Check remaining invites
        if ($user->remaining_invites <= 0) {
            return redirect()->back()->withErrors(['You do not have any remaining invites.']);
        }

        $invite = Invite::where('code', $request->invite_code)
            ->where('created_by', $user->id)
            ->where('is_active', true)
            ->first();

        if (!$invite || !$invite->isValid()) {
            return redirect()->back()->withErrors(['Your invite is no longer valid.']);
        }

        foreach ($request->emails as $email) {
            // Send the email
            Mail::to($email)->send(new InviteNotification($invite->code));

            // Increment the invite usage
            $invite->increment('times_used');
        }

        // Update user's remaining invites
        $user->decrement('remaining_invites', count($request->emails));

        return redirect()->route('user.invites.index')->with('success', 'Invites sent successfully!');
    }
}
