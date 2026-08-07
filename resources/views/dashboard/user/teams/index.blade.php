@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container py-4">

    @foreach (['success', 'error', 'info'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- Pending Assessments --}}
    @if($pendingAssessments->isNotEmpty())
        <div class="alert alert-warning border-warning shadow-sm">
            <div class="d-flex align-items-start gap-2">
                <i class="fas fa-clipboard-list fa-lg mt-1 text-warning"></i>
                <div class="flex-grow-1">
                    <strong>You have {{ $pendingAssessments->count() }} assessment{{ $pendingAssessments->count() > 1 ? 's' : '' }} waiting for your response</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($pendingAssessments as $assessment)
                            <li class="d-flex align-items-center justify-content-between gap-3 py-1">
                                <span>
                                    <strong>{{ $assessment->title }}</strong>
                                    <span class="text-muted small ms-1">— {{ $assessment->team->name }}</span>
                                </span>
                                <a href="{{ route('user.assessments.take', $assessment) }}" class="btn btn-warning btn-sm">
                                    Take Assessment <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Pending Invitations --}}
    @if($pendingInvites->count() > 0)
        <div class="alert alert-info border-info shadow-sm">
            <div class="d-flex align-items-start gap-2">
                <i class="fas fa-envelope-open-text fa-lg mt-1"></i>
                <div class="flex-grow-1">
                    <strong>You have {{ $pendingInvites->count() }} team invitation{{ $pendingInvites->count() > 1 ? 's' : '' }}</strong>
                    <ul class="mb-0 mt-2 list-unstyled">
                        @foreach($pendingInvites as $invite)
                            <li class="d-flex align-items-center justify-content-between gap-3 py-1 border-bottom border-opacity-25">
                                <span>
                                    Invited to join <strong>{{ $invite->team->name ?? 'a team' }}</strong>
                                    <span class="badge bg-secondary ms-1">{{ \App\Models\TeamMemberRole::labelFor($invite->role ?? 'member') }}</span>
                                    @if($invite->expires_at)
                                        <small class="text-muted ms-1">· expires {{ $invite->expires_at->format('d M') }}</small>
                                    @endif
                                </span>
                                <div class="d-flex gap-2">
                                    <form method="POST" action="{{ route('user.teams.invite.accept', $invite->code) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i> Accept
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('user.teams.invite.decline', $invite->code) }}"
                                          onsubmit="return confirm('Decline this invitation?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                            <i class="fas fa-times"></i> Decline
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- My Join Requests --}}
    @if($myPendingJoinRequests->isNotEmpty())
        <div class="alert alert-secondary border shadow-sm">
            <div class="d-flex align-items-start gap-2">
                <i class="fas fa-user-clock fa-lg mt-1"></i>
                <div class="flex-grow-1">
                    <strong>You have {{ $myPendingJoinRequests->count() }} pending join request{{ $myPendingJoinRequests->count() > 1 ? 's' : '' }}</strong>
                    <ul class="mb-0 mt-2 list-unstyled">
                        @foreach($myPendingJoinRequests as $request)
                            <li class="d-flex align-items-center justify-content-between gap-3 py-1 border-bottom border-opacity-25">
                                <span>
                                    Requested to join <strong>{{ $request->team->name ?? 'a team' }}</strong>
                                    <small class="text-muted ms-1">· sent {{ $request->created_at->diffForHumans() }}</small>
                                </span>
                                <form method="POST" action="{{ route('user.join-requests.cancel', $request) }}"
                                      onsubmit="return confirm('Withdraw this request?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-times"></i> Withdraw
                                    </button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-user-friends"></i> My Teams</h4>
        <div class="d-flex align-items-center gap-2">
            <div class="btn-group btn-group-sm" role="group" aria-label="Teams view toggle">
                <button type="button" id="teams-view-table-btn" class="btn btn-outline-secondary" onclick="setTeamsView('table')">
                    <i class="fas fa-list"></i> Table
                </button>
                <button type="button" id="teams-view-card-btn" class="btn btn-outline-secondary" onclick="setTeamsView('card')">
                    <i class="fas fa-th-large"></i> Cards
                </button>
            </div>
            <a href="{{ route('user.teams.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> New Team
            </a>
        </div>
    </div>

    {{-- Teams I Own --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong><span id="owned-teams-count">{{ $ownedTeams->total() }}</span> Teams I Own</strong>
            <x-search-box id="owned-teams-search" param="owned_search" page-param="owned_page" placeholder="Search my teams..." live />
        </div>
        <div class="card-body p-0" id="owned-teams-results" data-live-url="{{ route('user.teams.index', ['section' => 'owned']) }}">
            @include('dashboard.user.teams._owned-results', ['ownedTeams' => $ownedTeams])
        </div>
    </div>

    {{-- Teams I'm a Member Of --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong><span id="member-teams-count">{{ $memberTeams->total() }}</span> Teams I'm a Member Of</strong>
            <x-search-box id="member-teams-search" param="member_search" page-param="member_page" placeholder="Search teams I'm in..." live />
        </div>
        <div class="card-body p-0" id="member-teams-results" data-live-url="{{ route('user.teams.index', ['section' => 'member']) }}">
            @include('dashboard.user.teams._member-results', ['memberTeams' => $memberTeams])
        </div>
    </div>

</div>

<style>
    .team-card { transition: transform .15s ease, box-shadow .15s ease; }
    .team-card:hover { transform: translateY(-4px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.15) !important; }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function setTeamsView(mode) {
        document.querySelectorAll('.teams-view-table').forEach(el => el.classList.toggle('d-none', mode !== 'table'));
        document.querySelectorAll('.teams-view-cards').forEach(el => el.classList.toggle('d-none', mode !== 'card'));
        document.getElementById('teams-view-table-btn').classList.toggle('active', mode === 'table');
        document.getElementById('teams-view-card-btn').classList.toggle('active', mode === 'card');
        localStorage.setItem('teamsViewMode', mode);
    }

    // Initializes every not-yet-charted .maturity-radar canvas within `root`.
    // Scoped so it can be re-run after an AJAX swap inserts new canvases,
    // without re-initializing charts that already exist elsewhere on the page.
    function initMaturityRadars(root) {
        root.querySelectorAll('.maturity-radar').forEach(function (canvas) {
            const categories = JSON.parse(canvas.dataset.categories);
            const scores = JSON.parse(canvas.dataset.scores);
            const mini = canvas.dataset.mini === 'true';

            // A radar needs 3+ axes to read as a shape — with fewer, Chart.js
            // just draws an overlapping line, so fall back to a bar chart.
            if (categories.length < 3) {
                new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels: categories,
                        datasets: [{
                            label: 'Current Maturity',
                            data: scores,
                            backgroundColor: 'rgba(13, 110, 253, 0.6)',
                        }],
                    },
                    options: {
                        indexAxis: 'y',
                        maintainAspectRatio: false,
                        scales: {
                            x: { min: 0, max: 4, ticks: { stepSize: 1 } },
                            y: { ticks: { display: !mini } },
                        },
                        plugins: { legend: { display: false } },
                    },
                });
                return;
            }

            new Chart(canvas, {
                type: 'radar',
                data: {
                    labels: categories,
                    datasets: [{
                        label: 'Current Maturity',
                        data: scores,
                        backgroundColor: 'rgba(13, 110, 253, 0.2)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    scales: {
                        r: {
                            min: 0, max: 4, ticks: { stepSize: 1, display: !mini },
                            pointLabels: { display: !mini, font: { size: 11 } },
                        },
                    },
                    plugins: { legend: { display: false } },
                },
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        setTeamsView(localStorage.getItem('teamsViewMode') === 'card' ? 'card' : 'table');
        initMaturityRadars(document);

        [
            ['owned-teams-search', 'owned-teams-results', 'owned-teams-total-value', 'owned-teams-count'],
            ['member-teams-search', 'member-teams-results', 'member-teams-total-value', 'member-teams-count'],
        ].forEach(function ([searchId, resultsId, totalValueId, countId]) {
            initLiveSearch(searchId, resultsId, {
                onSwap: function () {
                    setTeamsView(localStorage.getItem('teamsViewMode') === 'card' ? 'card' : 'table');
                    const resultsEl = document.getElementById(resultsId);
                    if (resultsEl) {
                        initMaturityRadars(resultsEl);
                    }
                    const totalEl = document.getElementById(totalValueId);
                    if (totalEl) {
                        document.getElementById(countId).textContent = totalEl.textContent;
                    }
                },
            });
        });
    });
</script>
@endpush
