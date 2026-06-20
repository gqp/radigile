<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Http\Request;

class AdminTeamMemberController extends Controller
{
    public function index($teamId)
    {
        $team = Team::with('members.user')->findOrFail($teamId);

        return view('dashboard.admin.teams.members.index', compact('team'));
    }

    public function create($teamId)
    {
        $team = Team::findOrFail($teamId);

        $existingMemberIds = $team->members()->pluck('user_id')->toArray();
        $users = User::whereNotIn('id', $existingMemberIds)->get();

        return view('dashboard.admin.teams.members.create', compact('team', 'users'));
    }

    public function store(Request $request, $teamId)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role'    => 'required|string|in:admin,coach,member',
        ]);

        $team = Team::findOrFail($teamId);

        if (TeamMember::where('team_id', $team->id)->where('user_id', $validated['user_id'])->exists()) {
            return redirect()->back()->withErrors(['error' => 'The user is already a member of this team.']);
        }

        $team->members()->create([
            'user_id' => $validated['user_id'],
            'role'    => $validated['role'],
        ]);

        return redirect()->route('admin.teams.members.index', $teamId)->with('success', 'Team member added successfully!');
    }

    public function edit($teamId, $memberId)
    {
        $team = Team::findOrFail($teamId);
        $member = TeamMember::where('team_id', $teamId)->findOrFail($memberId);

        return view('dashboard.admin.teams.members.edit', compact('team', 'member'));
    }

    public function update(Request $request, $teamId, $memberId)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:admin,coach,member',
        ]);

        $member = TeamMember::where('team_id', $teamId)->findOrFail($memberId);

        $member->update(['role' => $validated['role']]);

        return redirect()->route('admin.teams.members.index', $teamId)->with('success', 'Team member updated successfully!');
    }

    public function destroy($teamId, $memberId)
    {
        $team = Team::findOrFail($teamId);

        $member = TeamMember::where('team_id', $team->id)->findOrFail($memberId);
        $member->delete();

        return redirect()->route('admin.teams.members.index', $teamId)->with('success', 'Team member removed successfully!');
    }
}
