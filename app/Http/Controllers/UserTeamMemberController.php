<?php

namespace App\Http\Controllers;

use App\Mail\TeamInviteNotification;
use App\Mail\TeamRegistrationInvite;
use App\Models\Setting;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamMember;
use App\Models\TeamMemberRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserTeamMemberController extends Controller
{
    // ── Search registered users ───────────────────────────────────────────────

    public function searchUsers(Request $request, Team $team): \Illuminate\Http\JsonResponse
    {
        abort_unless(auth()->id() === $team->owner_id, 403);

        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $excludeIds = $team->members()->pluck('users.id')
            ->push($team->owner_id)
            ->unique();

        $users = User::where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->whereNotIn('id', $excludeIds)
            ->limit(8)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    // ── Invite a registered user (creates invitation, requires acceptance) ───

    public function addMember(Request $request, Team $team): \Illuminate\Http\RedirectResponse
    {
        abort_unless(auth()->id() === $team->owner_id, 403);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => ['required', Rule::exists('team_member_roles', 'slug')],
        ]);

        if (!auth()->user()->canAddMemberToTeam($team)) {
            return back()->with('error', 'You have reached the maximum number of members allowed on your plan.');
        }

        $member = User::findOrFail($request->user_id);

        if ($team->members()->where('users.id', $member->id)->exists()) {
            return back()->with('error', "{$member->name} is already a member of this team.");
        }

        // Check no unexpired invite already pending
        if ($team->invitations()->where('email', $member->email)
                ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->exists()) {
            return back()->with('error', "An invitation is already pending for {$member->name}.");
        }

        $invitation = $team->invitations()->create([
            'email'      => $member->email,
            'role'       => $request->role,
            'code'       => Str::random(32),
            'created_by' => auth()->id(),
            'expires_at' => now()->addDays(7),
        ]);

        Mail::to($member->email)->queue(new TeamInviteNotification(
            $invitation->code,
            $team->name,
            route('user.teams.invite.accept', $invitation->code)
        ));

        return back()->with('success', "Invitation sent to {$member->name}. They'll appear as a member once they accept.");
    }

    // ── Invite multiple registered users at once ─────────────────────────────

    public function batchInvite(Request $request, Team $team): \Illuminate\Http\RedirectResponse
    {
        abort_unless(auth()->id() === $team->owner_id, 403);

        $request->validate([
            'members'        => 'required|array|min:1',
            'members.*.id'   => 'required|exists:users,id',
            'members.*.role' => ['required', Rule::exists('team_member_roles', 'slug')],
            'expires_in'     => 'required|in:0,1,3,7,14,30',
        ]);

        $expiresAt = (int) $request->input('expires_in') > 0
            ? now()->addDays((int) $request->input('expires_in'))
            : null;

        // Batch-fetch everything the loop needs up front instead of querying
        // per iteration — avoids 3+ queries per invitee on top of the mail send.
        $requestedIds = collect($request->input('members'))->pluck('id')->all();
        $users = User::whereIn('id', $requestedIds)->get()->keyBy('id');
        $existingMemberIds = $team->members()->pluck('users.id')->flip();
        $pendingInviteEmails = $team->invitations()
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->pluck('email')->flip();

        $sent    = 0;
        $skipped = [];

        foreach ($request->input('members') as $memberData) {
            $member = $users->get($memberData['id']);
            if (!$member || $member->id === $team->owner_id) continue;

            if ($existingMemberIds->has($member->id)) {
                $skipped[] = "{$member->name} (already a member)";
                continue;
            }

            if ($pendingInviteEmails->has($member->email)) {
                $skipped[] = "{$member->name} (invite already pending)";
                continue;
            }
            // Track within this batch too, in case the same user appears twice.
            $pendingInviteEmails->put($member->email, true);

            $invitation = $team->invitations()->create([
                'email'      => $member->email,
                'role'       => $memberData['role'],
                'code'       => Str::random(32),
                'created_by' => auth()->id(),
                'expires_at' => $expiresAt,
            ]);

            Mail::to($member->email)->queue(new TeamInviteNotification(
                $invitation->code,
                $team->name,
                route('user.teams.invite.accept', $invitation->code)
            ));

            $sent++;
        }

        $message = "{$sent} invitation(s) sent.";
        if (!empty($skipped)) {
            $message .= ' Skipped: ' . implode(', ', $skipped) . '.';
        }

        return back()->with($sent > 0 ? 'success' : 'info', $message);
    }

    // ── Invite an unregistered user ───────────────────────────────────────────

    public function invite(Request $request, Team $team): \Illuminate\Http\RedirectResponse
    {
        abort_unless(auth()->id() === $team->owner_id, 403);

        $validated = $request->validate([
            'email'      => 'required|email',
            'role'       => 'required|in:admin,coach,member',
            'expires_in' => 'required|in:1,3,7,14,30,0',
        ]);

        if (!auth()->user()->planHasFeature('team-invitations')) {
            return back()->with('error', 'Team invitations are not available on your current plan. Please upgrade.');
        }

        // Registered users should be added via search, not email invite
        if (User::where('email', $validated['email'])->exists()) {
            return back()->with('error', 'That email belongs to a registered Radagile user. Use the search box above to add them directly.');
        }

        if ($team->owner->email === $validated['email']) {
            return back()->with('error', 'That email address belongs to the team owner.');
        }

        if (!auth()->user()->canAddMemberToTeam($team)) {
            return back()->with('error', 'You have reached the maximum number of members allowed on your plan.');
        }

        // Don't re-send to someone already pending
        if ($team->invitations()->where('email', $validated['email'])
                ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
                ->exists()) {
            return back()->with('error', 'A pending invitation already exists for that email address.');
        }

        $expiresAt = (int) $validated['expires_in'] > 0
            ? now()->addDays((int) $validated['expires_in'])
            : null;

        $invitation = $team->invitations()->create([
            'email'      => $validated['email'],
            'role'       => $validated['role'],
            'code'       => Str::random(32),
            'created_by' => auth()->id(),
            'expires_at' => $expiresAt,
        ]);

        // Build registration URL — include invite code if invite-only mode is on
        $inviteOnly = Setting::get('invite_only');
        $registerParams = ['team_invite' => $invitation->code];

        if ($inviteOnly) {
            $registerParams['invite_code'] = auth()->user()->invite_code;
        }

        $registerUrl = route('register', $registerParams);
        $acceptUrl   = route('user.teams.invite.accept', $invitation->code);

        Mail::to($validated['email'])->queue(new TeamRegistrationInvite(
            teamName:   $team->name,
            role:       $validated['role'],
            registerUrl: $registerUrl,
            acceptUrl:  $acceptUrl,
            inviteOnly: (bool) $inviteOnly,
        ));

        return back()->with('success', "Registration invite sent to {$validated['email']}. They'll see the team invitation after signing up.");
    }

    // ── Accept an invitation ─────────────────────────────────────────────────

    public function acceptInvite(string $code): \Illuminate\Http\RedirectResponse
    {
        $invitation = TeamInvitation::where('code', $code)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->firstOrFail();

        $user = auth()->user();

        if ($invitation->email !== $user->email) {
            return redirect()->route('user.teams.index')
                ->with('error', 'This invitation was sent to a different email address.');
        }

        if ($invitation->team->members()->where('users.id', $user->id)->exists()) {
            $invitation->delete();
            return redirect()->route('user.teams.index')
                ->with('info', 'You are already a member of that team.');
        }

        TeamMember::create([
            'team_id' => $invitation->team_id,
            'user_id' => $user->id,
            'role'    => $invitation->role ?? 'member',
        ]);

        $teamName = $invitation->team->name;
        $teamId   = $invitation->team_id;
        $invitation->delete();

        return redirect()->route('user.teams.show', $teamId)
            ->with('success', "You have joined {$teamName}!");
    }

    // ── Decline an invitation ─────────────────────────────────────────────────

    public function declineInvite(string $code): \Illuminate\Http\RedirectResponse
    {
        $invitation = TeamInvitation::where('code', $code)->firstOrFail();

        if ($invitation->email !== auth()->user()->email) {
            return redirect()->route('user.teams.index')
                ->with('error', 'This invitation was not sent to your email address.');
        }

        $invitation->delete();

        return redirect()->route('user.teams.index')->with('info', 'Invitation declined.');
    }

    // ── Remove a member ───────────────────────────────────────────────────────

    public function removeMember(Team $team, User $member): \Illuminate\Http\RedirectResponse
    {
        abort_unless(auth()->id() === $team->owner_id, 403);

        if ($member->id === $team->owner_id) {
            return back()->with('error', 'You cannot remove the team owner.');
        }

        $team->members()->detach($member->id);

        return back()->with('success', "{$member->name} has been removed from the team.");
    }

    // ── Update a member's role ────────────────────────────────────────────────

    public function updateRole(Request $request, Team $team, User $member): \Illuminate\Http\RedirectResponse
    {
        abort_unless(auth()->id() === $team->owner_id, 403);

        $request->validate(['role' => 'required|in:admin,coach,member']);

        $team->members()->updateExistingPivot($member->id, ['role' => $request->role]);

        return back()->with('success', "{$member->name}'s role updated to " . ucfirst($request->role) . '.');
    }

    // ── Revoke a pending invitation ───────────────────────────────────────────

    public function revokeInvitation(Team $team, TeamInvitation $invitation): \Illuminate\Http\RedirectResponse
    {
        abort_unless(auth()->id() === $team->owner_id, 403);
        abort_unless($invitation->team_id === $team->id, 403);

        $email = $invitation->email;
        $invitation->delete();

        return back()->with('success', "Invitation to {$email} revoked.");
    }
}
