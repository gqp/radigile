<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\TeamMaturityService;
use Illuminate\Pagination\LengthAwarePaginator;

class TeamMaturityController extends Controller
{
    public function __construct(private TeamMaturityService $teamMaturityService)
    {
    }

    /**
     * Full maturity report across every team, sortable/searchable/paginated.
     *
     * Scores/trend/participation are computed in PHP from each team's
     * assessment history (not plain DB columns), so sorting and searching
     * happen against the built collection rather than via SQL — but
     * pagination still needs to slice that collection server-side so
     * sort/search stay correct across pages instead of only affecting
     * whatever happened to be on the current page.
     */
    public function index()
    {
        $teams = Team::with(['members', 'owner'])->get();
        $maturity = $this->teamMaturityService->build($teams);

        if ($search = trim((string) request('search'))) {
            $needle = strtolower($search);
            $maturity = $maturity->filter(function ($entry) use ($needle) {
                return str_contains(strtolower($entry['team']->name), $needle)
                    || str_contains(strtolower($entry['team']->owner->name ?? ''), $needle);
            });
        }

        $sort = request('sort', 'team');
        $dir = request('dir', 'asc');
        $descending = $dir === 'desc';

        $sortValue = match ($sort) {
            'owner' => fn ($entry) => strtolower($entry['team']->owner->name ?? ''),
            'overall' => fn ($entry) => $entry['overall'] ?? -1,
            'trend' => fn ($entry) => $entry['trend'] ?? 'zzz',
            'participation' => fn ($entry) => $entry['participation'] ?? -1,
            'lastAssessment' => fn ($entry) => $entry['assessment']?->updated_at?->timestamp ?? 0,
            default => fn ($entry) => strtolower($entry['team']->name),
        };

        $sorted = $maturity->sortBy($sortValue, SORT_REGULAR, $descending)->values();

        $perPage = 15;
        $page = (int) request('page', 1);
        $maturity = new LengthAwarePaginator(
            $sorted->forPage($page, $perPage)->values(),
            $sorted->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        if (request()->ajax()) {
            return view('dashboard.admin.team-maturity._results', compact('maturity'));
        }

        return view('dashboard.admin.team-maturity.index', compact('maturity'));
    }
}
