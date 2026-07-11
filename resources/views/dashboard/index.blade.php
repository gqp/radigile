@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')

@if(Auth::user()->hasPermissionTo('access-admin-panel'))
{{-- ── Admin Dashboard ── --}}
<div class="container mt-4">

    @foreach (['success', 'error', 'info'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <h4 class="mb-4">Admin Overview</h4>

    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $stats['users'] }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <a href="{{ route('admin.users.index') }}" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['teams'] }}</h3>
                    <p>Total Teams</p>
                </div>
                <div class="icon"><i class="fas fa-layer-group"></i></div>
                <a href="{{ route('admin.teams.index') }}" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['assessments'] }}</h3>
                    <p>Assessments</p>
                </div>
                <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                <a href="{{ route('user.assessments.index') }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['subscriptions'] }}</h3>
                    <p>Active Subscriptions</p>
                </div>
                <div class="icon"><i class="fas fa-handshake"></i></div>
                <a href="{{ route('admin.subscriptions.index') }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light"><strong><i class="fas fa-user-plus"></i> Recent Users</strong></div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Name</th><th>Email</th><th>Joined</th></tr>
                        </thead>
                        <tbody>
                            @foreach($recentUsers as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td class="text-muted small">{{ $u->email }}</td>
                                <td class="text-muted small">{{ $u->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">All Users</a>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-light"><strong><i class="fas fa-layer-group"></i> Recent Teams</strong></div>
                <div class="card-body p-0">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th>Team</th><th>Owner</th><th>Created</th></tr>
                        </thead>
                        <tbody>
                            @foreach($recentTeams as $team)
                            <tr>
                                <td><a href="{{ route('admin.teams.show', $team) }}">{{ $team->name }}</a></td>
                                <td class="text-muted small">{{ $team->owner->name ?? '—' }}</td>
                                <td class="text-muted small">{{ $team->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('admin.teams.index') }}" class="btn btn-sm btn-outline-primary">All Teams</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Team Maturity --}}
    <div class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fas fa-satellite-dish"></i> Team Maturity</h5>
            <a href="{{ route('admin.team-maturity.index') }}" class="btn btn-sm btn-outline-secondary">
                View All Teams <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        @if ($orgAverage !== null)
            @php
                $orgScoreClass = $orgAverage >= 3 ? 'bg-success' : ($orgAverage >= 2 ? 'bg-warning text-dark' : 'bg-danger');
            @endphp
            <div class="alert alert-light border shadow-sm d-flex align-items-center gap-3 mb-4">
                <span class="badge {{ $orgScoreClass }} fs-6">{{ number_format($orgAverage, 1) }} / 4</span>
                <span>
                    <strong>Org-wide average maturity</strong>
                    @if ($orgTrend === 'up')
                        <i class="fas fa-arrow-up text-success ms-1" title="Improved since previous assessments"></i>
                    @elseif ($orgTrend === 'down')
                        <i class="fas fa-arrow-down text-danger ms-1" title="Declined since previous assessments"></i>
                    @elseif ($orgTrend === 'flat')
                        <i class="fas fa-arrow-right text-muted ms-1" title="No significant change"></i>
                    @endif
                </span>
            </div>
        @endif

        @if ($topTeams->isEmpty() && $atRiskTeams->isEmpty())
            <p class="text-muted">No teams have completed an assessment yet.</p>
        @else
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light"><strong><i class="fas fa-trophy text-warning"></i> Top Teams</strong></div>
                    <div class="card-body">
                        @forelse ($topTeams as $entry)
                            <div class="row align-items-center gy-2 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                                <div class="col-5 col-sm-4">
                                    <canvas class="maturity-radar" height="140" data-mini="true"
                                            data-categories="{{ json_encode($entry['categories']) }}"
                                            data-scores="{{ json_encode($entry['scores']) }}"></canvas>
                                </div>
                                <div class="col-7 col-sm-8">
                                    <a href="{{ route('admin.teams.show', $entry['team']->id) }}" class="fw-semibold">{{ $entry['team']->name }}</a>
                                    <div class="mt-1">
                                        <span class="badge bg-success">{{ number_format($entry['overall'], 1) }} / 4</span>
                                        @if ($entry['trend'] === 'up')
                                            <i class="fas fa-arrow-up text-success ms-1"></i>
                                        @elseif ($entry['trend'] === 'down')
                                            <i class="fas fa-arrow-down text-danger ms-1"></i>
                                        @elseif ($entry['trend'] === 'flat')
                                            <i class="fas fa-arrow-right text-muted ms-1"></i>
                                        @endif
                                    </div>
                                    @if ($entry['weakestCategory'])
                                        <div class="text-muted small mt-1">Weakest: {{ $entry['weakestCategory']['name'] }} ({{ number_format($entry['weakestCategory']['score'], 1) }})</div>
                                    @endif
                                    @if ($entry['participation'] !== null)
                                        <div class="text-muted small">Participation: {{ $entry['participation'] }}%</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No rated teams yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light"><strong><i class="fas fa-triangle-exclamation text-danger"></i> Needs Attention</strong></div>
                    <div class="card-body">
                        @forelse ($atRiskTeams as $entry)
                            <div class="row align-items-center gy-2 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                                <div class="col-5 col-sm-4">
                                    <canvas class="maturity-radar" height="140" data-mini="true"
                                            data-categories="{{ json_encode($entry['categories']) }}"
                                            data-scores="{{ json_encode($entry['scores']) }}"></canvas>
                                </div>
                                <div class="col-7 col-sm-8">
                                    <a href="{{ route('admin.teams.show', $entry['team']->id) }}" class="fw-semibold">{{ $entry['team']->name }}</a>
                                    @if ($entry['isStale'])
                                        <span class="badge bg-warning text-dark ms-1"><i class="fas fa-clock"></i> Stale</span>
                                    @endif
                                    <div class="mt-1">
                                        <span class="badge bg-danger">{{ number_format($entry['overall'], 1) }} / 4</span>
                                        @if ($entry['trend'] === 'up')
                                            <i class="fas fa-arrow-up text-success ms-1"></i>
                                        @elseif ($entry['trend'] === 'down')
                                            <i class="fas fa-arrow-down text-danger ms-1"></i>
                                        @elseif ($entry['trend'] === 'flat')
                                            <i class="fas fa-arrow-right text-muted ms-1"></i>
                                        @endif
                                    </div>
                                    @if ($entry['weakestCategory'])
                                        <div class="text-muted small mt-1">Weakest: {{ $entry['weakestCategory']['name'] }} ({{ number_format($entry['weakestCategory']['score'], 1) }})</div>
                                    @endif
                                    @if ($entry['participation'] !== null)
                                        <div class="text-muted small">Participation: {{ $entry['participation'] }}%</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No teams need attention right now.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if ($mostImproved->isNotEmpty() || $mostDeclined->isNotEmpty())
        <div class="row g-4 mt-1">
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light"><strong><i class="fas fa-chart-line text-success"></i> Most Improved</strong></div>
                    <div class="card-body">
                        @forelse ($mostImproved as $entry)
                            <div class="row align-items-center gy-2 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                                <div class="col-5 col-sm-4">
                                    <canvas class="history-sparkline" height="140"
                                            data-labels="{{ json_encode($entry['history']->pluck('date')) }}"
                                            data-scores="{{ json_encode($entry['history']->pluck('overall')) }}"></canvas>
                                </div>
                                <div class="col-7 col-sm-8">
                                    <a href="{{ route('admin.teams.show', $entry['team']->id) }}" class="fw-semibold">{{ $entry['team']->name }}</a>
                                    <div class="mt-1">
                                        @php $deltaClass = $entry['delta'] > 0 ? 'bg-success' : ($entry['delta'] < 0 ? 'bg-danger' : 'bg-secondary'); @endphp
                                        <span class="badge {{ $deltaClass }}">{{ $entry['delta'] > 0 ? '+' : '' }}{{ number_format($entry['delta'], 1) }}</span>
                                        <span class="text-muted small ms-1">over {{ $entry['months'] }} assessments</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Not enough history yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light"><strong><i class="fas fa-chart-line text-danger"></i> Declining</strong></div>
                    <div class="card-body">
                        @forelse ($mostDeclined as $entry)
                            <div class="row align-items-center gy-2 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}">
                                <div class="col-5 col-sm-4">
                                    <canvas class="history-sparkline" height="140"
                                            data-labels="{{ json_encode($entry['history']->pluck('date')) }}"
                                            data-scores="{{ json_encode($entry['history']->pluck('overall')) }}"></canvas>
                                </div>
                                <div class="col-7 col-sm-8">
                                    <a href="{{ route('admin.teams.show', $entry['team']->id) }}" class="fw-semibold">{{ $entry['team']->name }}</a>
                                    <div class="mt-1">
                                        @php $deltaClass = $entry['delta'] > 0 ? 'bg-success' : ($entry['delta'] < 0 ? 'bg-danger' : 'bg-secondary'); @endphp
                                        <span class="badge {{ $deltaClass }}">{{ $entry['delta'] > 0 ? '+' : '' }}{{ number_format($entry['delta'], 1) }}</span>
                                        <span class="text-muted small ms-1">over {{ $entry['months'] }} assessments</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Not enough history yet.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@else
{{-- ── User Dashboard ── --}}
<div class="container mt-4">

    @foreach (['success', 'error', 'info'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <h4 class="mb-4">Welcome back, {{ Auth::user()->name }}!</h4>

    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $ownedTeams->count() }}</h3>
                    <p>Teams I Own</p>
                </div>
                <div class="icon"><i class="fas fa-user-shield"></i></div>
                <a href="{{ route('user.teams.index') }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $memberTeams->count() }}</h3>
                    <p>Teams I'm In</p>
                </div>
                <div class="icon"><i class="fas fa-user-friends"></i></div>
                <a href="{{ route('user.teams.index') }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="small-box {{ $pendingAssessments > 0 ? 'bg-warning' : 'bg-secondary' }}">
                <div class="inner">
                    <h3>{{ $pendingAssessments }}</h3>
                    <p>Pending Assessments</p>
                </div>
                <div class="icon"><i class="fas fa-clipboard-check"></i></div>
                <a href="{{ route('user.assessments.index') }}" class="small-box-footer">View <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-sm-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3 style="font-size:1.4rem;">{{ $subscription ? $subscription->plan->name : 'None' }}</h3>
                    <p>Current Plan</p>
                </div>
                <div class="icon"><i class="fas fa-credit-card"></i></div>
                @if($subscription && $subscription->plan->interval !== 'free')
                    <a href="{{ route('billing.portal') }}" class="small-box-footer">Manage <i class="fas fa-arrow-circle-right"></i></a>
                @else
                    <a href="{{ route('user.dashboard') }}" class="small-box-footer">Upgrade <i class="fas fa-arrow-circle-right"></i></a>
                @endif
            </div>
        </div>
    </div>

    {{-- Plan Card --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <strong><i class="fas fa-credit-card"></i> Your Plan</strong>
        </div>
        <div class="card-body">
            @if ($subscription)
                <div class="d-flex align-items-start justify-content-between flex-wrap gap-3">
                    <div>
                        <h5 class="mb-1">{{ $subscription->plan->name }}</h5>
                        <span class="badge bg-success">Active</span>
                        @if ($subscription->ends_at)
                            <small class="text-muted ms-2">Renews {{ $subscription->ends_at->format('M j, Y') }}</small>
                        @endif

                        {{-- Feature list --}}
                        @php $planFeatures = $subscription->plan->features ?? []; @endphp
                        @if(!empty($planFeatures))
                            <div class="mt-3">
                                <small class="text-muted fw-semibold d-block mb-1">Included features:</small>
                                @foreach ($planFeatures as $featureKey)
                                    <span class="badge bg-light text-dark border me-1 mb-1">
                                        <i class="fas fa-check text-success me-1"></i>
                                        {{ \App\Models\Plan::$availableFeatures[$featureKey] ?? $featureKey }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted small mt-2 mb-0">No additional features included on this plan.</p>
                        @endif

                        {{-- Limits --}}
                        <div class="mt-2">
                            <small class="text-muted">
                                Teams: <strong>{{ $subscription->plan->max_teams }}</strong> &nbsp;|&nbsp;
                                Members per team: <strong>{{ $subscription->plan->max_members }}</strong>
                            </small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        @if ($subscription->plan->interval === 'free')
                            @foreach (\App\Models\Plan::where('interval', '!=', 'free')->where('is_active', true)->get() as $plan)
                                <a href="{{ route('billing.checkout', $plan) }}" class="btn btn-primary btn-sm">
                                    Upgrade to {{ $plan->name }} — ${{ number_format($plan->price, 2) }}/{{ $plan->interval }}
                                </a>
                            @endforeach
                        @else
                            <a href="{{ route('billing.portal') }}" class="btn btn-outline-secondary btn-sm">Manage Billing</a>
                        @endif
                    </div>
                </div>
            @else
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                    <p class="mb-0 text-muted">You don't have an active plan.</p>
                    <form action="{{ route('subscribe.free') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Get Started Free</button>
                    </form>
                </div>
            @endif
        </div>
    </div>

    {{-- Quick links --}}
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light"><strong><i class="fas fa-user-friends"></i> My Teams</strong></div>
                <div class="card-body">
                    @forelse($myTeams as $entry)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>
                                {{ $entry['team']->name }}
                                <span class="badge {{ $entry['role'] === 'Owner' ? 'bg-warning text-dark' : 'bg-secondary' }} ms-1">{{ $entry['role'] }}</span>
                            </span>
                            <a href="{{ route('user.teams.show', $entry['team']) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </div>
                    @empty
                        <p class="text-muted mb-2">No teams yet.</p>
                    @endforelse
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('user.teams.index') }}" class="btn btn-sm btn-outline-secondary">All Teams</a>
                    <a href="{{ route('user.teams.create') }}" class="btn btn-sm btn-primary">New Team</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light"><strong><i class="fas fa-clipboard-list"></i> Assessments</strong></div>
                <div class="card-body">
                    <small class="text-muted fw-semibold d-block mb-2">
                        To Take
                        @if($pendingAssessments > 0)
                            <span class="badge bg-warning text-dark ms-1">{{ $pendingAssessments }}</span>
                        @endif
                    </small>
                    @forelse($assessmentsToTake as $assessment)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span>{{ $assessment->title }}</span>
                                <br>
                                <small class="text-muted">{{ $assessment->team->name ?? '—' }}</small>
                            </div>
                            <a href="{{ route('user.assessments.take', $assessment) }}" class="btn btn-sm btn-primary">Take</a>
                        </div>
                    @empty
                        <p class="text-muted mb-2 small">Nothing waiting for your response.</p>
                    @endforelse

                    <hr>

                    <small class="text-muted fw-semibold d-block mb-2">Taken</small>
                    @forelse($assessmentsTaken as $assessment)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span>{{ $assessment->title }}</span>
                                <br>
                                <small class="text-muted">{{ $assessment->team->name ?? '—' }} &middot; {{ ucfirst($assessment->status) }}</small>
                            </div>
                            @if ($assessment->isClosed())
                                <a href="{{ route('user.assessments.results', $assessment) }}" class="btn btn-sm btn-outline-primary">Results</a>
                            @else
                                <span class="badge bg-light text-dark border">Awaiting others</span>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-2 small">You haven't taken any assessments yet.</p>
                    @endforelse

                    <hr>

                    <small class="text-muted fw-semibold d-block mb-2">Created by You</small>
                    @forelse($myCreatedAssessments as $assessment)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span>{{ $assessment->title }}</span>
                                <br>
                                <small class="text-muted">{{ $assessment->team->name ?? '—' }} &middot; {{ ucfirst($assessment->status) }}</small>
                            </div>
                            @if ($assessment->isClosed())
                                <a href="{{ route('user.assessments.results', $assessment) }}" class="btn btn-sm btn-outline-primary">Results</a>
                            @else
                                <a href="{{ route('user.assessments.show', $assessment) }}" class="btn btn-sm btn-outline-primary">View</a>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-2 small">You haven't created any assessments yet.</p>
                    @endforelse
                </div>
                <div class="card-footer text-end">
                    <a href="{{ route('user.assessments.index') }}" class="btn btn-sm btn-outline-secondary">All Assessments</a>
                    <a href="{{ route('user.assessments.create') }}" class="btn btn-sm btn-primary">New Assessment</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Team Maturity --}}
    @if ($teamMaturity->isNotEmpty())
    <div class="mt-4">
        <h5 class="mb-3"><i class="fas fa-satellite-dish"></i> Team Maturity</h5>
        <div class="row g-3">
            @foreach ($teamMaturity as $entry)
            <div class="col-md-6 col-xl-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <strong>{{ $entry['team']->name }}</strong>
                        @if ($entry['overall'] !== null)
                            <span class="badge bg-primary">{{ number_format($entry['overall'], 1) }} / 4</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($entry['assessment'])
                            <canvas class="maturity-radar" height="220"
                                    data-categories="{{ json_encode($entry['categories']) }}"
                                    data-scores="{{ json_encode($entry['scores']) }}"></canvas>
                            <p class="text-muted small mt-2 mb-0">
                                From "{{ $entry['assessment']->title }}" &middot; closed {{ $entry['assessment']->updated_at->format('M j, Y') }}
                            </p>
                        @else
                            <p class="text-muted mb-0">No completed assessments yet for this team.</p>
                        @endif
                    </div>
                    @if ($entry['assessment'])
                    <div class="card-footer text-end">
                        <a href="{{ route('user.assessments.results', $entry['assessment']) }}" class="btn btn-sm btn-outline-primary">View Full Results</a>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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

    document.querySelectorAll('.history-sparkline').forEach(function (canvas) {
        const labels = JSON.parse(canvas.dataset.labels);
        const scores = JSON.parse(canvas.dataset.scores);
        const color = scores[scores.length - 1] >= scores[0] ? 'rgba(25, 135, 84, 1)' : 'rgba(220, 53, 69, 1)';

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    data: scores,
                    borderColor: color,
                    backgroundColor: 'transparent',
                    tension: 0.3,
                    pointRadius: 2,
                    pointBackgroundColor: color,
                }],
            },
            options: {
                scales: {
                    x: { display: false },
                    y: { min: 0, max: 4, display: false },
                },
                plugins: { legend: { display: false } },
            },
        });
    });
</script>
@endpush
