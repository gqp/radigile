<?php

namespace App\Http\Controllers\Admin;

use App\Models\Team;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TeamInvitationController extends Controller
{
    /**
     * Accept a team invitation.
     */
    public function accept(Request $request)
    {
        // Retrieve and validate the inputs
        $teamId = $request->query('team');
        $userId = $request->query('user');
        $inviteCode = $request->query('invite_code'); // Optional: Invite code if tied to acceptance

        // Ensure valid inputs are provided
        if (!$teamId || !$userId || !$inviteCode) {
            return redirect()->route('home')->with('error', 'Invalid or missing invitation details.');
        }

        // Find the team based on the provided ID
        $team = Team::find($teamId);

        // Check if the team exists
        if (!$team) {
            return redirect()->route('home')->with('error', 'Invalid team. No such team exists.');
        }

        // Find the invite based on the invite code
        $invite = Invite::where('code', $inviteCode)
            ->where('email', auth()->check() ? auth()->user()->email : null) // Additional check for logged-in user
            ->first();

        // Ensure the invite exists, is active, and is still valid
        if (!$invite || !$invite->is_active || !$invite->isValid()) {
            return redirect()->route('home')->with('error', 'The invitation is invalid, expired, or no longer active.');
        }

        // Find the user attempting to accept the invitation
        $user = auth()->check() ? auth()->user() : User::find($userId);

        if (!$user || $user->email !== $invite->email) {
            return redirect()->route('home')->with('error', 'Unauthorized access or invalid user.');
        }

        // Update the invite usage
        $invite->times_used += 1;
        if ($invite->times_used >= $invite->max_uses) {
            $invite->is_active = false; // Disable the invite if max uses are reached
        }
        $invite->save();

        // Attach the user to the team with the appropriate role
        $team->members()->attach($user->id, ['role' => 'member']);

        return redirect()->route('teams.index')->with('success', 'You have joined the team successfully!');
    }
}

