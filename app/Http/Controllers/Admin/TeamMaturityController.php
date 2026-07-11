<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use App\Services\TeamMaturityService;

class TeamMaturityController extends Controller
{
    public function __construct(private TeamMaturityService $teamMaturityService)
    {
    }

    /**
     * Full maturity report across every team, sortable/searchable client-side.
     */
    public function index()
    {
        $teams = Team::with(['members', 'owner'])->get();
        $maturity = $this->teamMaturityService->build($teams);

        return view('dashboard.admin.team-maturity.index', compact('maturity'));
    }
}
