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
        $user = Auth::user();

        return view('dashboard.user.invite', [
            'remainingInvites' => $user->remaining_invites, // User's remaining invites
        ]);
    }

    /**
     * Handle invite sending.
     */
    public function sendInvites(Request $request)
    {
        $request->validate([
            'emails' => 'required|array',
            'emails.*' => 'required|email',
            'amounts' => 'required|array',
            'amounts.*' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        $totalToSend = array_sum($request->amounts);

        // Check if user has enough remaining invites
        if ($user->remaining_invites < $totalToSend) {
            return redirect()->back()->withErrors(['error' => 'Not enough remaining invites.']);
        }

        foreach ($request->emails as $index => $email) {
            $amount = $request->amounts[$index];
            $code = Str::random(10); // Generate a unique invite code

            // Create the invite
            Invite::create([
                'code' => $code,
                'created_by' => $user->id,
                'max_uses' => $amount,
            ]);

            // Send the invite via email
            Mail::to($email)->send(new \App\Mail\InviteNotification($code));

            // Deduct the invite count
            $user->decrement('remaining_invites', $amount);
        }

        return redirect()->back()->with('success', 'Invites sent successfully.');
    }
}
