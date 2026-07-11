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
        <div class="card-header bg-light">
            <strong>Teams I Own ({{ $ownedTeams->count() }})</strong>
        </div>
        <div class="card-body p-0">
            @if ($ownedTeams->isEmpty())
                <p class="text-muted p-3 mb-0">You don't own any teams yet. <a href="{{ route('user.teams.create') }}">Create one</a>.</p>
            @else
                {{-- Table view --}}
                <div class="table-responsive teams-view-table">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Maturity</th>
                                <th>Framework</th>
                                <th class="text-center">Members</th>
                                <th class="text-center">Pending</th>
                                <th>Created</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($ownedTeams as $team)
                            <tr>
                                <td>
                                    <strong>{{ $team->name }}</strong>
                                    @if($team->description)
                                        <br><small class="text-muted">{{ Str::limit($team->description, 60) }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @include('dashboard.partials.mini-radar', ['team' => $team, 'size' => 44])
                                </td>
                                <td class="text-muted small">{{ $team->team_frameq->name ?? '—' }}</td>
                                <td class="text-center">{{ $team->members->count() }}</td>
                                <td class="text-center">
                                    @if($team->pending_count > 0)
                                        <a href="{{ route('user.teams.show', $team->id) }}"
                                           class="badge bg-warning text-dark text-decoration-none"
                                           title="{{ $team->pending_count }} invitation(s) awaiting response">
                                            {{ $team->pending_count }}
                                        </a>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $team->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('user.teams.show', $team->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    <a href="{{ route('user.teams.edit', $team->id) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Card view --}}
                <div class="row g-4 p-3 teams-view-cards d-none">
                    @foreach ($ownedTeams as $team)
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 shadow border-0 team-card">
                            <div class="card-header bg-primary text-white d-flex align-items-center gap-2 py-3">
                                <i class="fas fa-users fa-lg"></i>
                                <h5 class="mb-0 fw-bold text-truncate">{{ $team->name }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center mb-3">
                                    @include('dashboard.partials.mini-radar', ['team' => $team, 'size' => 110])
                                </div>
                                @if($team->description)
                                    <p class="text-muted mb-3">{{ Str::limit($team->description, 80) }}</p>
                                @endif
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="fas fa-diagram-project me-1"></i> {{ $team->team_frameq->name ?? '—' }}
                                    </span>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="fas fa-user-friends me-1"></i> {{ $team->members->count() }} member{{ $team->members->count() === 1 ? '' : 's' }}
                                    </span>
                                    @if($team->pending_count > 0)
                                        <span class="badge bg-warning text-dark px-3 py-2">
                                            <i class="fas fa-envelope me-1"></i> {{ $team->pending_count }} pending
                                        </span>
                                    @endif
                                </div>
                                <p class="text-muted small mb-0"><i class="fas fa-calendar me-1"></i> Created {{ $team->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex gap-2 pb-3">
                                <a href="{{ route('user.teams.show', $team->id) }}" class="btn btn-primary flex-fill">View</a>
                                <a href="{{ route('user.teams.edit', $team->id) }}" class="btn btn-outline-warning flex-fill">Edit</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Teams I'm a Member Of --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <strong>Teams I'm a Member Of ({{ $memberTeams->count() }})</strong>
        </div>
        <div class="card-body p-0">
            @if ($memberTeams->isEmpty())
                <p class="text-muted p-3 mb-0">You haven't been added to any teams yet.</p>
            @else
                {{-- Table view --}}
                <div class="table-responsive teams-view-table">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th class="text-center">Maturity</th>
                                <th>Owner</th>
                                <th>Framework</th>
                                <th>My Role</th>
                                <th>Joined</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($memberTeams as $team)
                            <tr>
                                <td>
                                    <strong>{{ $team->name }}</strong>
                                    @if($team->description)
                                        <br><small class="text-muted">{{ Str::limit($team->description, 60) }}</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @include('dashboard.partials.mini-radar', ['team' => $team, 'size' => 44])
                                </td>
                                <td class="text-muted small">{{ $team->owner->name ?? '—' }}</td>
                                <td class="text-muted small">{{ $team->team_frameq->name ?? '—' }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($team->pivot->role ?? 'member') }}</span></td>
                                <td class="text-muted small">{{ $team->pivot->created_at?->format('d M Y') ?? '—' }}</td>
                                <td class="text-end">
                                    <a href="{{ route('user.teams.show', $team->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Card view --}}
                <div class="row g-4 p-3 teams-view-cards d-none">
                    @foreach ($memberTeams as $team)
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 shadow border-0 team-card">
                            <div class="card-header bg-secondary text-white d-flex align-items-center gap-2 py-3">
                                <i class="fas fa-user-friends fa-lg"></i>
                                <h5 class="mb-0 fw-bold text-truncate">{{ $team->name }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center mb-3">
                                    @include('dashboard.partials.mini-radar', ['team' => $team, 'size' => 110])
                                </div>
                                @if($team->description)
                                    <p class="text-muted mb-3">{{ Str::limit($team->description, 80) }}</p>
                                @endif
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="fas fa-crown me-1"></i> {{ $team->owner->name ?? '—' }}
                                    </span>
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="fas fa-diagram-project me-1"></i> {{ $team->team_frameq->name ?? '—' }}
                                    </span>
                                    <span class="badge bg-info text-dark px-3 py-2">
                                        {{ ucfirst($team->pivot->role ?? 'member') }}
                                    </span>
                                </div>
                                <p class="text-muted small mb-0"><i class="fas fa-calendar me-1"></i> Joined {{ $team->pivot->created_at?->format('d M Y') ?? '—' }}</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 pb-3">
                                <a href="{{ route('user.teams.show', $team->id) }}" class="btn btn-primary w-100">View</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
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

    document.addEventListener('DOMContentLoaded', function () {
        setTeamsView(localStorage.getItem('teamsViewMode') === 'card' ? 'card' : 'table');
    });

    document.querySelectorAll('.maturity-radar').forEach(function (canvas) {
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
</script>
@endpush
