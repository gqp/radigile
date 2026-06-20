<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TeamDomain;
use App\Models\TeamFramework;
use App\Models\Invite;
use Illuminate\Support\Facades\Mail;
use App\Mail\TeamInviteNotification;

class UserTeamController extends Controller
{
    /**
     * Display a listing of teams the authenticated user belongs to or owns.
     */
    public function index()
    {
        $user = Auth::user();

        // Fetch pending invites for the authenticated user
        $pendingInvites = Invite::where('email', $user->email) // Use email or id as needed
        ->where(function ($query) {
            $query->whereNull('expires_at') // Active invites
            ->orWhere('expires_at', '>', now());
        })
            ->with('team') // Ensure relationships are eager loaded
            ->get();

        // Fetch teams the user owns
        $ownedTeams = $user->teamsOwned ?? collect(); // Use null coalescing to avoid null errors

        // Fetch teams the user is a member of
        $memberTeams = $user->teamsMemberOf ?? collect(); // Use null coalescing to avoid null errors

        // Pass variables to the view
        return view('dashboard.user.teams.index', [
            'ownedTeams' => $ownedTeams,
            'memberTeams' => $memberTeams,
            'pendingInvites' => $pendingInvites,
        ]);
    }

    /**
     * Display the specified team that the user owns or is part of.
     */
    public function show($id)
    {
        // Fetch the specific team with members, ensuring it belongs to the auth user
        $team = Team::with('members.user')
            ->where('id', $id)
            ->ownedBy(Auth::id())
            ->firstOrFail();

        // Pass the team data to the corresponding Blade view
        return view('dashboard.user.teams.show', compact('team'));
    }

    public function create()
    {
        // Fetch required data for the form
        $users = User::all();
        $teamDomains = TeamDomain::all(); // Assuming TeamDomain contains data about team domains
        $teamFrameworks = TeamFramework::all();     // Assuming TeamType contains information about types

        // Pass the data to the view
        return view('dashboard.user.teams.create', compact('teamDomains', 'teamFrameworks', 'users'));
    }

    /**
     * Handle the submission of the "Create Team" form.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!Team::canCreateMoreTeams($user)) {
            return redirect()->route('user.teams.index')
                ->with('error', 'You have reached the maximum number of teams allowed on your current plan.');
        }

        // Validate the form data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'team_domain_id' => 'required|exists:team_domains,id',   // Validate team domain relationship
            'team_framework_id' => 'required|exists:team_frameworks,id', // Validate team framework relationship
        ], [
            // Custom error messages
            'name.required' => 'The team name is required.',
            'team_domain_id.required' => 'Please select a valid team domain.',
            'team_framework_id.required' => 'Please select a valid team framework.',
        ]);

        // Create the team and associate it with the authenticated user
        $team = new Team();
        $team->name = $validated['name'];
        $team->description = $validated['description'] ?? null;
        $team->team_domain_id = $validated['team_domain_id'];
        $team->team_framework_id = $validated['team_framework_id'];
        $team->owner_id = Auth::id();
        $team->save();

        // Redirect to the index page with a success message
        return redirect()->route('user.teams.index')->with('success', 'Team created successfully!');
    }

    /**
     * Show the form to edit the specified team.
     */
    public function edit($id)
    {
        // Fetch the team with members while ensuring the authenticated user is the owner
        $team = Team::with('members')
            ->where('id', $id)
            ->where('owner_id', Auth::id()) // Ensure the authenticated user is the owner
            ->firstOrFail();

        // Fetch required data for edit form dropdowns
        $teamDomains = TeamDomain::all();
        $teamFrameworks = TeamFramework::all();
        $users = User::all();
        $members = $team->members;

        // Pass the team, users, members, and dropdown data to the edit view
        return view('dashboard.user.teams.edit', compact('team', 'users', 'members', 'teamDomains', 'teamFrameworks'));
    }

    /**
     * Update details of the team that the authenticated user owns.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:private,public',
            'team_domain_id' => 'required|exists:team_domains,id',
            'team_framework_id' => 'required|exists:team_frameworks,id',
            'invite_emails' => 'nullable|string', // Optional invites
        ], [
            'team_domain_id.required' => 'Please select a valid team domain.',
            'team_framework_id.required' => 'Please select a valid team framework.',
        ]);

        $team = Team::findOrFail($id);

        $this->authorize('update', $team);

        // Update team details
        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'team_domain_id' => $validated['team_domain_id'],
            'team_framework_id' => $validated['team_framework_id'],
        ]);

        // Process invitation emails if provided
        $emails = explode(',', $validated['invite_emails'] ?? '');
        $emails = array_filter(array_map('trim', $emails)); // Clean up email list

        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $invite = $team->invitations()->create([
                    'email' => $email,
                    'code' => \Str::random(10),
                    'created_by' => auth()->id(),
                    'expires_at' => now()->addDays(7),
                ]);

                // Send invite email
                Mail::to($email)->send(new TeamInviteNotification(
                    $invite->code,
                    $team->name,
                    route('user.teams.invite.accept', $invite->code)
                ));
            }
        }

        return redirect()->route('user.teams.edit', $team->id)->with('success', 'Team updated successfully, and invitations sent!');
    }

}
