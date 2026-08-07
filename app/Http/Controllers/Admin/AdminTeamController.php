<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\Searchable;
use App\Http\Controllers\Concerns\Sortable;
use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Models\User;
use App\Models\TeamDomain;
use App\Models\TeamFramework;
use Illuminate\Http\Request;

class AdminTeamController extends Controller
{
    use Sortable, Searchable;

    /**
     * Display a listing of all teams.
     */
    public function index()
    {
        $query = Team::with(['owner', 'members']);
        $this->applySearch($query, ['name', 'description', 'owner.name']);
        $this->applySort($query, ['name', 'created_at'], 'created_at', 'desc');
        $teams = $query->paginate(15)->withQueryString();

        if (request()->ajax()) {
            return view('dashboard.admin.teams._results', compact('teams'));
        }

        $teamDomains = TeamDomain::cached();
        $teamFrameworks = TeamFramework::cached();

        return view('dashboard.admin.teams.index', compact('teams', 'teamDomains', 'teamFrameworks'));
    }

    /**
     * Show the form to create a new team.
     */
    public function create()
    {
        $users = User::all(); // List all registered users
        $teamDomains = TeamDomain::cached(); // Retrieve all team domains
        $teamFrameworks = TeamFramework::cached(); // Retrieve all team frameworks

        return view('dashboard.admin.teams.create', compact('users', 'teamDomains', 'teamFrameworks'));
    }

    /**
     * Store a newly created team in the database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:public,private',
            'team_framework' => 'nullable|exists:team_frameworks,id',
            'team_domain_id' => 'required|exists:team_domains,id',
            'description' => 'nullable|string',
            'owner_id' => 'required|exists:users,id',
            'team_members' => 'nullable|array',
            'team_members.*' => 'email',
        ]);

        // Create the team
        $team = Team::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'team_framework_id' => $validated['team_framework'] ?? null,
            'team_domain_id' => $validated['team_domain_id'],
            'description' => $validated['description'] ?? null,
            'owner_id' => $validated['owner_id'],
        ]);

        // Process each invited member email — batch-fetch registered users up
        // front instead of one query per email.
        $emails = $validated['team_members'] ?? [];
        $registeredUsers = User::whereIn('email', $emails)->get()->keyBy('email');

        foreach ($emails as $email) {
            $registeredUser = $registeredUsers->get($email);

            if ($registeredUser) {
                // Send invitation to registered user
                $acceptUrl = route('team.invitation.accept', ['team' => $team->id, 'user' => $registeredUser->id]);
                Mail::to($email)->queue(new RegisteredUserInviteNotification($team->name, $acceptUrl));

                // Optionally attach the user to the team with a pending role
                $team->members()->attach($registeredUser->id, ['role' => 'pending']);
            } else {
                // Send invitation to non-registered user
                $inviteCode = Str::random(10);
                $registrationUrl = route('register') . "?invite_code={$inviteCode}";

                Invite::create([
                    'code' => $inviteCode,
                    'created_by' => $validated['owner_id'],
                    'email' => $email,
                    'max_uses' => 1,
                    'is_active' => true,
                ]);

                Mail::to($email)->queue(new InviteNotification($inviteCode, $registrationUrl));
            }
        }

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Team created and invites sent successfully!');
    }

    public function show($id)
    {
        $team = Team::with(['owner', 'members', 'domain', 'team_frameq', 'assessments.questions', 'assessments.results'])->findOrFail($id);
        return view('dashboard.admin.teams.show', compact('team'));
    }

    public function edit($id)
    {
        // Fetch the team by its ID
        $team = Team::findOrFail($id);

        // Fetch any additional data you need for the edit form
        $teamDomains = TeamDomain::cached(); // Assuming you have TeamDomain models
        $teamFrameworks = TeamFramework::cached(); // Assuming you have TeamFramework models

        // Fetch users for assigning owner or members
        $users = User::all(); // Adjust this based on your filtering logic

        return view('dashboard.admin.teams.edit', compact('team', 'teamDomains', 'teamFrameworks', 'users'));
    }

    /**
     * Update an existing team.
     */
    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_domain_id' => 'required|exists:team_domains,id',
            'team_framework_id' => 'nullable|exists:team_frameworks,id',
            'owner_id' => 'required|exists:users,id',
            'team_members' => 'nullable|array',
            'team_members.*' => 'exists:users,id',
        ]);

        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'team_domain_id' => $validated['team_domain_id'],
            'team_framework_id' => $validated['team_framework_id'] ?? null,
            'owner_id' => $validated['owner_id'],
            'open_to_join_requests' => $request->boolean('open_to_join_requests'),
        ]);

        $team->members()->sync($validated['team_members'] ?? []);

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Team updated successfully!');
    }

    /**
     * Delete a team.
     */
    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Team deleted successfully!');
    }
}
