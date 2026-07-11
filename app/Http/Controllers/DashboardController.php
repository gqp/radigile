<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\Team;
use App\Models\User;
use App\Models\Subscription;
use App\Services\TeamMaturityService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct(private TeamMaturityService $teamMaturityService)
    {
    }

    public function index()
    {
        $user = Auth::user();

        if ($user->hasPermissionTo('access-admin-panel')) {
            return $this->adminDashboard();
        }

        return $this->userDashboard($user);
    }

    private function adminDashboard()
    {
        $stats = [
            'users'         => User::count(),
            'teams'         => Team::count(),
            'assessments'   => Assessment::count(),
            'subscriptions' => Subscription::where('is_active', true)->count(),
        ];

        $recentUsers = User::latest()->limit(5)->get();
        $recentTeams = Team::with('owner')->latest()->limit(5)->get();

        $maturity = Cache::remember('admin.team_maturity_snapshot', now()->addMinutes(15), function () {
            return $this->teamMaturityService->build(Team::with('members')->get());
        });

        $ratedTeams = $maturity->filter(fn ($entry) => $entry['overall'] !== null)->values();
        [$topTeams, $atRiskTeams] = $this->splitRanking($ratedTeams->sortByDesc('overall')->values());

        $orgAverage = $ratedTeams->isNotEmpty() ? round($ratedTeams->avg('overall'), 2) : null;

        $withPrevious = $ratedTeams->filter(fn ($entry) => $entry['previousOverall'] !== null);
        $orgPreviousAverage = $withPrevious->isNotEmpty() ? round($withPrevious->avg('previousOverall'), 2) : null;

        $orgTrend = null;
        if ($orgAverage !== null && $orgPreviousAverage !== null) {
            $diff = round($orgAverage - $orgPreviousAverage, 2);
            $orgTrend = $diff > 0.05 ? 'up' : ($diff < -0.05 ? 'down' : 'flat');
        }

        $trends = Cache::remember('admin.team_trend_snapshot', now()->addMinutes(15), function () {
            return $this->teamMaturityService->trends(Team::with('members')->get());
        });

        $trendedTeams = $trends->filter(fn ($entry) => $entry['delta'] !== null)->values();
        [$mostImproved, $mostDeclined] = $this->splitRanking($trendedTeams->sortByDesc('delta')->values());

        return view('dashboard.index', compact(
            'stats', 'recentUsers', 'recentTeams',
            'topTeams', 'atRiskTeams', 'orgAverage', 'orgTrend',
            'mostImproved', 'mostDeclined'
        ));
    }

    /**
     * Splits a desc-sorted collection into a top slice and a bottom slice
     * (reversed to worst-first) with no overlap, even when the collection
     * is small — used for both current-score and trend-delta rankings.
     */
    private function splitRanking(Collection $sortedDesc): array
    {
        $total = $sortedDesc->count();
        $bottomCount = $total > 6 ? 3 : intdiv($total, 2);
        $topCount = $total > 6 ? 3 : $total - $bottomCount;

        $top = $sortedDesc->take($topCount)->values();
        $bottom = $sortedDesc->slice($topCount, $bottomCount)->reverse()->values();

        return [$top, $bottom];
    }

    private function userDashboard(User $user)
    {
        $ownedTeams   = $user->teamsOwned ?? collect();
        $memberTeams  = $user->teamsMemberOf ?? collect();
        $subscription = $user->activeSubscription();

        // Assessments relevant to the user that are still active and awaiting
        // their response — via team membership/ownership or an evaluator invite.
        $toTakeQuery = Assessment::where('created_by', '!=', $user->id)
            ->where('status', 'active')
            ->whereDoesntHave('results', fn($q) => $q->where('user_id', $user->id))
            ->where(function ($q) use ($user) {
                $q->where(function ($teamQ) use ($user) {
                        $teamQ->whereHas('team', function ($tq) use ($user) {
                                $tq->where('owner_id', $user->id)
                                   ->orWhereHas('members', fn($m) => $m->where('users.id', $user->id));
                            })
                            ->whereDoesntHave('excludedMembers', fn($e) => $e->where('user_id', $user->id));
                    })
                    ->orWhereHas('evaluators', fn($e) => $e->where('user_id', $user->id));
            });

        $pendingAssessments = (clone $toTakeQuery)->count();

        $assessmentsToTake = (clone $toTakeQuery)->with('team')->latest()->limit(3)->get();

        $myCreatedAssessments = Assessment::where('created_by', $user->id)
            ->with('team')
            ->latest()
            ->limit(3)
            ->get();

        // Assessments someone else created that this user has already responded to.
        $assessmentsTaken = Assessment::where('created_by', '!=', $user->id)
            ->whereHas('results', fn ($q) => $q->where('user_id', $user->id))
            ->with('team')
            ->latest()
            ->limit(3)
            ->get();

        $teams = $ownedTeams->merge($memberTeams)->unique('id')->load('members');
        $teamMaturity = $this->teamMaturityService->build($teams);

        $myTeams = $ownedTeams->map(fn ($team) => ['team' => $team, 'role' => 'Owner'])
            ->concat($memberTeams->map(fn ($team) => ['team' => $team, 'role' => 'Member']))
            ->take(3);

        return view('dashboard.index', compact(
            'ownedTeams', 'memberTeams', 'subscription', 'myTeams',
            'pendingAssessments', 'assessmentsToTake', 'myCreatedAssessments', 'assessmentsTaken', 'teamMaturity'
        ));
    }
}
