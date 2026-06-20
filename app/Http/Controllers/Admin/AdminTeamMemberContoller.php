<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTeamMemberController extends Controller
{
    /**
     * Display a listing of all team members for a given team.
     */
    public function index($teamId)
    {
        $team = Team::with('members.user')->findOrFail($teamId);

        return view('dashboard.admin.teams.members.index', compact('team'));
    }

    /**
     * Show the form to add a new team member.
     */
    public function create($teamId)
    {
        $team = Team::findOrFail($teamId);

        // Fetch all users who are not already team members
        $existingMemberIds = $team->members()->pluck('user_id')->toArray();
        $users = User::whereNotIn('id', $existingMemberIds)->get();

        return view('dashboard.admin.teams.members.create', compact('team', 'users'));
    }

    /**
     * Store a new team member in the database.
     */
    public function store(Request $request, $teamId)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|string|in:admin,coach,member',
        ]);

        $team = Team::findOrFail($teamId);

        // Check if the user is already a member
        if (TeamMember::where('team_id', $team->id)->where('user_id', $validated['user_id'])->exists()) {
            return redirect()->back()->withErrors(['error' => 'The user is already a member of this team.']);
        }

        // Add the new member
        $team->members()->create([
            'user_id' => $validated['user_id'],
            'role'    => $validated['role'],
        ]);

        return redirect()->route('admin.teams.members.index', $teamId)->with('success', 'Team member added successfully!');
    }

    /**
     * Show the form to edit a team member's details.
     */
    public function edit($teamId, $memberId)
    {
        $team = Team::findOrFail($teamId);
        $member = TeamMember::where('team_id', $teamId)->findOrFail($memberId);

        return view('dashboard.admin.teams.members.edit', compact('team', 'member'));
    }

    /**
     * Update a specific team member's details (role, etc.).
     */
    public function update(Request $request, $teamId, $memberId)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:admin,coach,member',
        ]);

        $member = TeamMember::where('team_id', $teamId)->findOrFail($memberId);

        // Update the role of the team member
        $member->update([
            'role' => $validated['role'],
        ]);

        return redirect()->route('admin.teams.members.index', $teamId)->with('success', 'Team member updated successfully!');
    }

    /**
     * Remove a specific team member from the database.
     */
    public function destroy($teamId, $memberId)
    {
        $team = Team::findOrFail($teamId);

        $member = TeamMember::where('team_id', $team->id)->findOrFail($memberId);
        $member->delete();

        return redirect()->route('admin.teams.members.index', $teamId)->with('success', 'Team member removed successfully!');
    }
}
