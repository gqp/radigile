<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\Searchable;
use App\Http\Controllers\Concerns\Sortable;
use App\Models\Assessment;
use App\Models\AssessmentTemplatePublishRequest;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\TeamJoinRequest;
use App\Models\TeamMemberRole;
use App\Models\User;
use App\Services\TeamMaturityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\TeamDomain;
use App\Models\TeamFramework;

class UserTeamController extends Controller
{
    use Sortable, Searchable;

    public function __construct(private TeamMaturityService $teamMaturityService)
    {
    }

    /**
     * Display a listing of teams the authenticated user belongs to or owns.
     */
    public function index()
    {
        $user = Auth::user();

        $pendingInvites = TeamInvitation::where('email', $user->email)
            ->where(fn($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->with('team')
            ->get();

        $ownedQuery = $user->teamsOwned()
            ->with(['team_frameq', 'members'])
            ->withCount(['invitations as pending_count' => fn($q) =>
                $q->where(fn($q2) => $q2->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ])
            ->withCount(['joinRequests as pending_requests_count' => fn($q) => $q->pending()]);
        $this->applySearch($ownedQuery, ['name', 'description'], 'owned_search');
        $this->applySort($ownedQuery, ['name', 'created_at'], 'created_at', 'desc', 'owned_sort', 'owned_dir');
        $ownedTeams = $ownedQuery->paginate(15, ['*'], 'owned_page')->withQueryString();

        $memberQuery = $user->teamsMemberOf()
            ->with(['owner', 'team_frameq'])
            ->withPivot('role', 'created_at');
        $this->applySearch($memberQuery, ['name', 'description'], 'member_search');
        $this->applySort($memberQuery, ['name'], 'name', 'asc', 'member_sort', 'member_dir');
        $memberTeams = $memberQuery->paginate(15, ['*'], 'member_page')->withQueryString();

        $pendingAssessments = Assessment::where('status', 'active')
            ->whereHas('team', function ($q) use ($user) {
                $q->where('owner_id', $user->id)
                  ->orWhereHas('members', fn($m) => $m->where('users.id', $user->id));
            })
            ->whereDoesntHave('results', fn($q) => $q->where('user_id', $user->id))
            ->with('team')
            ->get();

        $teamIds = collect($ownedTeams->items())->merge(collect($memberTeams->items()))->pluck('id')->unique();
        $allTeams = Team::with('members')->whereIn('id', $teamIds)->get();
        $maturityByTeamId = $this->teamMaturityService->build($allTeams)->keyBy(fn ($entry) => $entry['team']->id);

        $myPendingJoinRequests = TeamJoinRequest::where('user_id', $user->id)
            ->pending()
            ->with('team')
            ->latest()
            ->get();

        if (request()->ajax()) {
            $partials = [
                'owned'  => ['dashboard.user.teams._owned-results', ['ownedTeams' => $ownedTeams]],
                'member' => ['dashboard.user.teams._member-results', ['memberTeams' => $memberTeams]],
            ];
            [$view, $data] = $partials[request('section')] ?? $partials['owned'];
            return view($view, [...$data, 'maturityByTeamId' => $maturityByTeamId]);
        }

        return view('dashboard.user.teams.index', [
            'ownedTeams'             => $ownedTeams,
            'memberTeams'            => $memberTeams,
            'pendingInvites'         => $pendingInvites,
            'pendingAssessments'     => $pendingAssessments,
            'maturityByTeamId'       => $maturityByTeamId,
            'myPendingJoinRequests'  => $myPendingJoinRequests,
        ]);
    }

    /**
     * Display the specified team that the user owns or is part of.
     */
    public function show($id)
    {
        $userId = Auth::id();

        $team = Team::with(['members', 'owner', 'domain', 'team_frameq', 'assessments.questions', 'assessments.results'])
            ->where('id', $id)
            ->where(function ($q) use ($userId) {
                $q->where('owner_id', $userId)
                  ->orWhereHas('members', fn($m) => $m->where('users.id', $userId));
            })
            ->firstOrFail();

        $isOwner = $team->owner_id === $userId;

        $outgoingInvitations = $isOwner
            ? $team->invitations()
                ->with('createdBy')
                ->orderByDesc('created_at')
                ->get()
            : collect();

        $memberRoles = TeamMemberRole::allOrdered();

        $maturity = $this->teamMaturityService->build(collect([$team]))->first();

        $opportunities = collect();
        if ($maturity['assessment']) {
            $opportunities = $maturity['categories']->zip($maturity['scores'])
                ->map(fn ($pair) => ['name' => $pair[0], 'score' => $pair[1]])
                ->sortBy('score')
                ->take(3)
                ->values();
        }

        $maturityHistory = $this->teamMaturityService->history($team);

        $canApproveJoinRequests = $team->canApproveJoinRequests(Auth::user());
        $pendingJoinRequests = $canApproveJoinRequests
            ? $team->joinRequests()->pending()->with('user')->latest()->get()
            : collect();

        $canManageAssessments = $team->canManageAssessments(Auth::user());
        $teamTemplates = $canManageAssessments
            ? $team->templates()->withCount('questions')->latest()->get()
            : collect();
        $pendingTemplatePublishRequests = $canManageAssessments
            ? AssessmentTemplatePublishRequest::whereIn('assessment_template_id', $team->templates()->pluck('id'))
                ->pending()->get()->keyBy('assessment_template_id')
            : collect();

        return view('dashboard.user.teams.show', compact(
            'team', 'isOwner', 'outgoingInvitations', 'memberRoles', 'maturity', 'opportunities', 'maturityHistory',
            'canApproveJoinRequests', 'pendingJoinRequests',
            'canManageAssessments', 'teamTemplates', 'pendingTemplatePublishRequests'
        ));
    }

    public function create()
    {
        $teamDomains    = TeamDomain::cached();
        $teamFrameworks = TeamFramework::cached();
        $memberRoles    = TeamMemberRole::allOrdered();

        return view('dashboard.user.teams.create', compact('teamDomains', 'teamFrameworks', 'memberRoles'));
    }

    public function searchUsers(Request $request): \Illuminate\Http\JsonResponse
    {
        $q = trim($request->input('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $users = User::where('id', '!=', Auth::id())
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%");
            })
            ->limit(8)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
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

        $request->validate([
            'members'        => 'nullable|array',
            'members.*.id'   => 'required|exists:users,id',
            'members.*.role' => ['required', Rule::exists('team_member_roles', 'slug')],
        ]);

        $team = Team::create([
            'name'              => $validated['name'],
            'description'       => $validated['description'] ?? null,
            'team_domain_id'    => $validated['team_domain_id'],
            'team_framework_id' => $validated['team_framework_id'],
            'owner_id'          => Auth::id(),
        ]);

        // Send invitations to pre-selected members — they must accept before joining.
        // Batch-fetch up front instead of one query per member.
        $memberIds = collect($request->input('members', []))->pluck('id');
        $members = User::whereIn('id', $memberIds)->get()->keyBy('id');

        $inviteCount = 0;
        foreach ($request->input('members', []) as $memberData) {
            if ((int) $memberData['id'] === Auth::id()) continue;

            $member = $members->get($memberData['id']);
            if (!$member) continue;

            $invitation = $team->invitations()->create([
                'email'      => $member->email,
                'role'       => $memberData['role'],
                'code'       => \Illuminate\Support\Str::random(32),
                'created_by' => Auth::id(),
                'expires_at' => now()->addDays(7),
            ]);

            \Illuminate\Support\Facades\Mail::to($member->email)->queue(
                new \App\Mail\TeamInviteNotification(
                    $invitation->code,
                    $team->name,
                    route('user.teams.invite.accept', $invitation->code)
                )
            );

            $inviteCount++;
        }

        $message = $inviteCount > 0
            ? "Team created! {$inviteCount} invitation(s) sent — members will appear once they accept."
            : 'Team created! Use the team page to invite members.';

        return redirect()->route('user.teams.show', $team->id)->with('success', $message);
    }

    /**
     * Show the form to edit the specified team.
     */
    public function edit($id)
    {
        $team = Team::where('id', $id)
            ->where('owner_id', Auth::id())
            ->firstOrFail();

        $teamDomains    = TeamDomain::cached();
        $teamFrameworks = TeamFramework::cached();

        return view('dashboard.user.teams.edit', compact('team', 'teamDomains', 'teamFrameworks'));
    }

    /**
     * Update details of the team that the authenticated user owns.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'description'       => 'nullable|string',
            'type'              => 'required|in:private,public',
            'team_domain_id'    => 'required|exists:team_domains,id',
            'team_framework_id' => 'required|exists:team_frameworks,id',
        ], [
            'team_domain_id.required'    => 'Please select a valid team domain.',
            'team_framework_id.required' => 'Please select a valid team framework.',
        ]);

        $team = Team::findOrFail($id);

        abort_unless(Auth::id() === $team->owner_id, 403);

        // Update team details
        $team->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'team_domain_id' => $validated['team_domain_id'],
            'team_framework_id' => $validated['team_framework_id'],
            'open_to_join_requests' => $request->boolean('open_to_join_requests'),
        ]);

        return redirect()->route('user.teams.show', $team->id)->with('success', 'Team updated successfully!');
    }

}
