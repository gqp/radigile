<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamMember;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\TeamInvitation;

class UserTeamMemberController extends Controller
{
    /**
     * Invite a user to a team.
     */
    public function invite(Request $request)
    {
        $validated = $request->validate([
            'team_id' => 'required|exists:teams,id',
            'email' => 'required|email',
            'role' => 'required|string|in:admin,coach,member',
        ]);

        $team = Team::findOrFail($validated['team_id']);

        // Ensure the inviter is the team owner
        $this->authorize('addMember', $team);

        $invitee = User::where('email', $validated['email'])->first();

        // If the user is registered
        if ($invitee) {
            // Check if already a member
            if ($this->isUserAlreadyMember($team->id, $invitee->id)) {
                return response()->json(['error' => 'User is already part of the team.'], 422);
            }

            // Create an invitation for a registered user
            $invite = $team->invitations()->create([
                'email' => $validated['email'],
                'role' => $validated['role'],
                'code' => \Str::random(10),
                'created_by' => auth()->id(),
                'expires_at' => now()->addDays(7), // Expire after 7 days
            ]);

            // Send an email with the accept link
            \Mail::to($validated['email'])->send(new \App\Mail\TeamInviteNotification(
                $invite->code,
                $team->name,
                route('user.teams.invite.accept', $invite->code)
            ));

        } else {
            // Non-registered users

            // Check if Invite-Only Mode is enabled
            $inviteOnly = Setting::where('name', 'invite_only')->value('value');

            // Generate registration invite code (if "Invite-Only" is on)
            $invitationCode = $inviteOnly ? auth()->user()->invite_code : null;

            // Send a registration invite email
            \Mail::to($validated['email'])->send(new \App\Mail\InviteNotification($invitationCode));
        }

        return response()->json(['message' => 'Invitation sent successfully.']);
    }


    /**
     * Check if a user is already a member of the given team.
     *
     * @param int $teamId
     * @param int|null $userId
     * @return bool
     */
    private function isUserAlreadyMember(int $teamId, ?int $userId): bool
    {
        if (!$userId) {
            return false;
        }

        return TeamMember::isMember($userId, $teamId)->exists();
    }

    /**
     * Get the user ID by their email address.
     *
     * @param string $email
     * @return int|null
     */
    private function getUserIdByEmail(string $email): ?int
    {
        return User::where('email', $email)->value('id');
    }

    public function acceptInvite($code)
    {
        $invite = TeamInvitation::where('code', $code)
            ->where('expires_at', '>', now())
            ->firstOrFail();

        $userId = auth()->id();

        // Check if the user is already a member
        $alreadyMember = TeamMember::where('team_id', $invite->team_id)
            ->where('user_id', $userId)
            ->exists();

        if ($alreadyMember) {
            return redirect()->back()->withErrors(['You are already a member of this team.']);
        }

        // Add the user to the team
        TeamMember::create([
            'team_id' => $invite->team_id,
            'user_id' => $userId,
            'role' => $invite->role,
        ]);

        // Delete the invitation
        $invite->delete();

        return redirect()->route('user.teams.index')->with('success', 'You have successfully joined the team!');
    }


}
