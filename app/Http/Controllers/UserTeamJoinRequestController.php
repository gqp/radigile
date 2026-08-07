<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Searchable;
use App\Http\Controllers\Concerns\Sortable;
use App\Mail\TeamJoinRequestApproved;
use App\Mail\TeamJoinRequestRejected;
use App\Mail\TeamJoinRequestSubmitted;
use App\Models\Team;
use App\Models\TeamJoinRequest;
use App\Models\TeamMember;
use App\Models\TeamMemberRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class UserTeamJoinRequestController extends Controller
{
    use Sortable, Searchable;

    /**
     * Browse teams open to join requests, excluding teams the user already
     * owns or belongs to.
     */
    public function browse()
    {
        $user = auth()->user();

        $memberTeamIds = $user->teamsMemberOf()->pluck('teams.id');
        $ownedTeamIds = $user->teamsOwned()->pluck('id');
        $excludeIds = $memberTeamIds->merge($ownedTeamIds)->unique();

        $query = Team::with('owner')
            ->openToJoinRequests()
            ->whereNotIn('id', $excludeIds);
        $this->applySearch($query, ['name', 'description']);
        $this->applySort($query, ['name', 'created_at'], 'name', 'asc');
        $teams = $query->paginate(15)->withQueryString();

        $pendingTeamIds = TeamJoinRequest::where('user_id', $user->id)
            ->pending()
            ->pluck('team_id');

        if (request()->ajax()) {
            return view('dashboard.user.teams._browse-results', compact('teams', 'pendingTeamIds'));
        }

        return view('dashboard.user.teams.browse', compact('teams', 'pendingTeamIds'));
    }

    /**
     * Submit a request to join a team.
     */
    public function store(Team $team)
    {
        $user = auth()->user();

        abort_unless($team->open_to_join_requests, 403, 'This team is not accepting join requests.');

        if ($user->id === $team->owner_id || $team->members()->where('users.id', $user->id)->exists()) {
            return back()->with('error', 'You are already part of this team.');
        }

        if (TeamJoinRequest::where('team_id', $team->id)->where('user_id', $user->id)->pending()->exists()) {
            return back()->with('error', 'You already have a pending request to join this team.');
        }

        request()->validate(['message' => 'nullable|string|max:1000']);

        $joinRequest = TeamJoinRequest::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'message' => request('message'),
        ]);

        foreach ($this->approversFor($team) as $approver) {
            Mail::to($approver->email)->queue(new TeamJoinRequestSubmitted($joinRequest, $approver->name));
        }

        return back()->with('success', "Your request to join {$team->name} has been sent for approval.");
    }

    /**
     * Withdraw a pending request the current user made.
     */
    public function cancel(TeamJoinRequest $joinRequest)
    {
        abort_unless(auth()->id() === $joinRequest->user_id && $joinRequest->isPending(), 403);

        $joinRequest->delete();

        return back()->with('info', 'Your join request has been withdrawn.');
    }

    /**
     * Approve a pending join request — creates the team membership.
     */
    public function approve(TeamJoinRequest $joinRequest)
    {
        abort_unless($joinRequest->team->canApproveJoinRequests(auth()->user()), 403);
        abort_unless($joinRequest->isPending(), 404);

        TeamMember::create([
            'team_id' => $joinRequest->team_id,
            'user_id' => $joinRequest->user_id,
            'role' => TeamMemberRole::default()?->slug ?? 'member',
        ]);

        $joinRequest->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        Mail::to($joinRequest->user->email)->queue(new TeamJoinRequestApproved($joinRequest));

        return back()->with('success', "{$joinRequest->user->name} has been added to {$joinRequest->team->name}.");
    }

    /**
     * Reject a pending join request.
     */
    public function reject(TeamJoinRequest $joinRequest)
    {
        abort_unless($joinRequest->team->canApproveJoinRequests(auth()->user()), 403);
        abort_unless($joinRequest->isPending(), 404);

        $joinRequest->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        Mail::to($joinRequest->user->email)->queue(new TeamJoinRequestRejected($joinRequest));

        return back()->with('info', "The request from {$joinRequest->user->name} has been declined.");
    }

    /**
     * The owner plus any member whose role can approve join requests.
     */
    private function approversFor(Team $team)
    {
        $roleSlugs = $team->members->pluck('pivot.role')->unique()->filter();

        $permittedRoleSlugs = TeamMemberRole::whereIn('slug', $roleSlugs)
            ->get()
            ->filter(fn (TeamMemberRole $role) => $role->hasPermission('approve-join-requests'))
            ->pluck('slug');

        $approvers = $team->members->filter(
            fn (User $member) => $permittedRoleSlugs->contains($member->pivot->role)
        );

        if ($team->owner && !$approvers->contains('id', $team->owner_id)) {
            $approvers->push($team->owner);
        }

        return $approvers;
    }
}
