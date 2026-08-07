@extends('layouts.app')

@section('content')
<div class="container py-4">

    @foreach (['success', 'error', 'info'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-1">{{ $team->name }}</h2>
            <p class="text-muted mb-0">
                <span class="badge bg-secondary me-1">{{ $team->domain->name ?? '—' }}</span>
                <span class="badge bg-info text-dark">{{ $team->team_frameq->name ?? '—' }}</span>
                @if($isOwner)
                    <span class="badge bg-warning text-dark ms-1">Owner</span>
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            @if(auth()->user()->planHasFeature('create-assessments'))
                <a href="{{ route('user.assessments.create', ['team_id' => $team->id]) }}" class="btn btn-success btn-sm">
                    <i class="fas fa-clipboard-plus"></i> New Assessment
                </a>
            @endif
            @if ($isOwner)
                <a href="{{ route('user.teams.edit', $team->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-pencil-alt"></i> Edit Team
                </a>
            @endif
            <a href="{{ route('user.teams.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
    </div>

    {{-- Team Maturity & Areas of Opportunity --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong><i class="fas fa-satellite-dish"></i> Team Maturity</strong>
            @if ($maturity['overall'] !== null)
                @php
                    $scoreClass = $maturity['overall'] >= 3 ? 'bg-success' : ($maturity['overall'] >= 2 ? 'bg-warning text-dark' : 'bg-danger');
                @endphp
                <span class="badge {{ $scoreClass }}">{{ number_format($maturity['overall'], 1) }} / 4</span>
            @endif
        </div>
        <div class="card-body">
            @if ($maturity['assessment'])
                <div class="row g-4">
                    <div class="col-md-6">
                        <canvas id="teamMaturityRadar" height="220"
                                data-categories="{{ json_encode($maturity['categories']) }}"
                                data-scores="{{ json_encode($maturity['scores']) }}"></canvas>
                        <p class="text-muted small mt-2 mb-0">
                            From "{{ $maturity['assessment']->title }}" &middot; closed {{ $maturity['assessment']->updated_at->format('M j, Y') }}
                            @if ($maturity['trend'] === 'up')
                                &middot; <i class="fas fa-arrow-up text-success"></i> improved since last assessment
                            @elseif ($maturity['trend'] === 'down')
                                &middot; <i class="fas fa-arrow-down text-danger"></i> declined since last assessment
                            @elseif ($maturity['trend'] === 'flat')
                                &middot; <i class="fas fa-arrow-right text-muted"></i> no significant change
                            @endif
                        </p>
                        @if ($maturity['isStale'])
                            <span class="badge bg-warning text-dark mt-2">
                                <i class="fas fa-clock"></i> Last assessment closed over 90 days ago
                            </span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6 class="mb-3"><i class="fas fa-bullseye text-danger"></i> Areas of Opportunity</h6>
                        @forelse ($opportunities as $opportunity)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $opportunity['name'] }}</span>
                                <span class="badge bg-danger">{{ number_format($opportunity['score'], 1) }} / 4</span>
                            </div>
                        @empty
                            <p class="text-muted">No category data available.</p>
                        @endforelse

                        @if ($maturity['participation'] !== null)
                            <p class="text-muted small mt-3 mb-0">
                                <i class="fas fa-users"></i> {{ $maturity['participation'] }}% of members responded
                            </p>
                        @endif

                        <div class="mt-3">
                            <a href="{{ route('user.assessments.results', $maturity['assessment']) }}" class="btn btn-sm btn-outline-primary">
                                View Full Results
                            </a>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-muted mb-0">
                    This team hasn't completed an assessment yet.
                    @if(auth()->user()->planHasFeature('create-assessments'))
                        <a href="{{ route('user.assessments.create', ['team_id' => $team->id]) }}">Create one</a>
                        to see maturity data and areas of opportunity.
                    @endif
                </p>
            @endif
        </div>
    </div>

    {{-- Maturity Over Time --}}
    @if ($maturityHistory->count() >= 2)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light"><strong><i class="fas fa-chart-line"></i> Maturity Over Time</strong></div>
        <div class="card-body">
            <canvas id="teamMaturityHistory" height="80"
                    data-labels="{{ json_encode($maturityHistory->pluck('date')) }}"
                    data-scores="{{ json_encode($maturityHistory->pluck('overall')) }}"></canvas>
        </div>
    </div>
    @endif

    <div class="row g-4">

        {{-- Left column: Details + Invite panel --}}
        <div class="col-lg-4">

            {{-- Team Details --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light"><strong><i class="fas fa-info-circle"></i> Team Details</strong></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Owner</dt>
                        <dd class="col-sm-7">{{ $team->owner->name ?? '—' }}</dd>
                        <dt class="col-sm-5">Domain</dt>
                        <dd class="col-sm-7">{{ $team->domain->name ?? '—' }}</dd>
                        <dt class="col-sm-5">Framework</dt>
                        <dd class="col-sm-7">{{ $team->team_frameq->name ?? '—' }}</dd>
                        <dt class="col-sm-5">Members</dt>
                        <dd class="col-sm-7">{{ $team->members->count() }}</dd>
                        <dt class="col-sm-5">Created</dt>
                        <dd class="col-sm-7">{{ $team->created_at->format('d M Y') }}</dd>
                        @if ($team->description)
                            <dt class="col-sm-5">Description</dt>
                            <dd class="col-sm-7">{{ $team->description }}</dd>
                        @endif
                    </dl>
                </div>
            </div>

            {{-- Add / Invite Panel (owner only) --}}
            @if ($isOwner)
                <div class="card shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <strong><i class="fas fa-user-plus"></i> Add Members</strong>
                        <span id="memberCount" class="badge bg-primary" style="display:none;"></span>
                    </div>
                    <div class="card-body">

                        {{-- ── Search input ── --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Search by name or email</label>
                            <div class="position-relative">
                                <input type="text" id="memberSearch" class="form-control"
                                       placeholder="e.g. John or john@example.com" autocomplete="off">
                                <div id="searchResults" class="position-absolute w-100 bg-white border rounded shadow-sm mt-1"
                                     style="z-index:1000; display:none; max-height:220px; overflow-y:auto;"></div>
                            </div>
                            <div id="searchSpinner" class="text-muted small mt-1" style="display:none;">
                                <i class="fas fa-spinner fa-spin"></i> Searching...
                            </div>
                        </div>

                        {{-- ── Selected users (chips) ── --}}
                        <div id="selectedMembers" class="mb-3" style="display:none;">
                            <div class="d-flex flex-wrap gap-2" id="memberChips"></div>
                        </div>

                        {{-- ── Batch invite form ── --}}
                        <form method="POST" action="{{ route('user.teams.members.batch', $team->id) }}" id="batchInviteForm" style="display:none;">
                            @csrf
                            <div id="memberInputs"></div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Invitation Expires</label>
                                <select name="expires_in" class="form-select form-select-sm">
                                    <option value="7" selected>In 7 days</option>
                                    <option value="1">In 1 day</option>
                                    <option value="3">In 3 days</option>
                                    <option value="14">In 14 days</option>
                                    <option value="30">In 30 days</option>
                                    <option value="0">Never expires</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane"></i> Send Invites (<span id="inviteCountLabel">0</span>)
                            </button>
                            <p class="text-muted small mt-2 mb-0">
                                They'll get an email and appear as members once they accept.
                            </p>
                        </form>

                        {{-- ── Invite unregistered user ── --}}
                        <div id="inviteUnregisteredSection" style="display:none;">
                            @if(auth()->user()->planHasFeature('team-invitations'))
                                <div class="alert alert-info py-2 px-3 mb-3">
                                    <i class="fas fa-info-circle me-1"></i>
                                    No Radigile account found for <strong id="notFoundEmail"></strong>.
                                    Send them a registration invite and they'll be added once they sign up.
                                </div>
                                <form method="POST" action="{{ route('user.teams.members.invite', $team->id) }}" id="inviteForm">
                                    @csrf
                                    <input type="hidden" name="email" id="inviteEmail">
                                    @error('email')<div class="alert alert-danger py-1 small">{{ $message }}</div>@enderror
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Role</label>
                                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                                            @foreach ($memberRoles as $r)
                                                <option value="{{ $r->slug }}" {{ old('role', $memberRoles->firstWhere('is_default', true)?->slug) === $r->slug ? 'selected' : '' }}>{{ $r->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Invitation Expires</label>
                                        <select name="expires_in" class="form-select" required>
                                            <option value="7"  selected>In 7 days</option>
                                            <option value="1">In 1 day</option>
                                            <option value="3">In 3 days</option>
                                            <option value="14">In 14 days</option>
                                            <option value="30">In 30 days</option>
                                            <option value="0">Never expires</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-paper-plane"></i> Send Registration Invite
                                    </button>
                                </form>
                            @else
                                <div class="alert alert-warning py-2 px-3 mb-0">
                                    <i class="fas fa-lock me-1"></i>
                                    No Radigile account found. Sending registration invites requires a higher plan.
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            @endif
        </div>

        {{-- Right column: Members + Pending Invitations + Assessments --}}
        <div class="col-lg-8">

            {{-- Members --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <strong><i class="fas fa-users"></i> Members ({{ $team->members->count() + 1 }})</strong>
                </div>
                <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                        @if ($isOwner)<th></th>@endif
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Owner --}}
                                    <tr>
                                        <td>{{ $team->owner->name ?? '—' }}</td>
                                        <td class="text-muted small">{{ $team->owner->email ?? '—' }}</td>
                                        <td><span class="badge bg-warning text-dark">Owner</span></td>
                                        <td class="text-muted small">{{ $team->created_at?->format('d M Y') ?? '—' }}</td>
                                        @if ($isOwner)<td></td>@endif
                                    </tr>

                                    @if ($team->members->isEmpty())
                                    <tr>
                                        <td colspan="{{ $isOwner ? 5 : 4 }}" class="text-muted small text-center py-3">
                                            No other members yet. Invite someone using the panel on the left.
                                        </td>
                                    </tr>
                                    @endif

                                    @foreach ($team->members as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td class="text-muted small">{{ $member->email }}</td>
                                        <td>
                                            @if ($isOwner)
                                                <form method="POST" action="{{ route('user.teams.members.update-role', [$team->id, $member->id]) }}" class="d-flex gap-1">
                                                    @csrf @method('PATCH')
                                                    <select name="role" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                                                        @foreach ($memberRoles as $r)
                                                            <option value="{{ $r->slug }}" {{ $member->pivot->role === $r->slug ? 'selected' : '' }}>
                                                                {{ $r->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </form>
                                            @else
                                                <span class="badge bg-secondary">{{ $memberRoles->firstWhere('slug', $member->pivot->role)?->name ?? ucfirst($member->pivot->role ?? 'member') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">{{ $member->pivot->created_at?->format('d M Y') ?? '—' }}</td>
                                        @if ($isOwner)
                                        <td class="text-end">
                                            <form method="POST" action="{{ route('user.teams.members.remove', [$team->id, $member->id]) }}"
                                                  onsubmit="return confirm('Remove {{ $member->name }} from this team?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-user-minus"></i>
                                                </button>
                                            </form>
                                        </td>
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                </div>
            </div>

            {{-- Pending (outgoing) Invitations — owner only --}}
            @if ($isOwner && $outgoingInvitations->isNotEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <strong><i class="fas fa-envelope"></i> Pending Invitations ({{ $outgoingInvitations->count() }})</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Expires</th>
                                    <th>Sent</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($outgoingInvitations as $inv)
                                @php $expired = $inv->expires_at && $inv->expires_at->isPast(); @endphp
                                <tr class="{{ $expired ? 'table-secondary' : '' }}">
                                    <td>{{ $inv->email }}</td>
                                    <td><span class="badge bg-secondary">{{ $memberRoles->firstWhere('slug', $inv->role)?->name ?? ucfirst($inv->role ?? 'member') }}</span></td>
                                    <td class="small {{ $expired ? 'text-danger' : 'text-muted' }}">
                                        @if ($inv->expires_at)
                                            {{ $expired ? 'Expired ' : '' }}{{ $inv->expires_at->format('d M Y') }}
                                        @else
                                            Never
                                        @endif
                                    </td>
                                    <td class="text-muted small">{{ $inv->created_at->diffForHumans() }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('user.teams.invitations.revoke', [$team->id, $inv->id]) }}"
                                              onsubmit="return confirm('Revoke this invitation?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                <i class="fas fa-times"></i> Revoke
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Join Requests — visible to owner/approvers only --}}
            @if ($canApproveJoinRequests && $pendingJoinRequests->isNotEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <strong><i class="fas fa-user-clock"></i> Join Requests ({{ $pendingJoinRequests->count() }})</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Requester</th>
                                    <th>Message</th>
                                    <th>Requested</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingJoinRequests as $request)
                                <tr>
                                    <td>
                                        <strong>{{ $request->user->name }}</strong>
                                        <br><small class="text-muted">{{ $request->user->email }}</small>
                                    </td>
                                    <td class="text-muted small">{{ $request->message ? Str::limit($request->message, 80) : '—' }}</td>
                                    <td class="text-muted small">{{ $request->created_at->diffForHumans() }}</td>
                                    <td class="text-end">
                                        <form method="POST" action="{{ route('user.join-requests.approve', $request) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('user.join-requests.reject', $request) }}" class="d-inline"
                                              onsubmit="return confirm('Reject this request?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Reject</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Assessment Templates — visible to owners/permitted members only --}}
            @if ($canManageAssessments && $teamTemplates->isNotEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <strong><i class="bi bi-bookmark-star"></i> Assessment Templates ({{ $teamTemplates->count() }})</strong>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Questions</th>
                                    <th>Scope</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($teamTemplates as $template)
                                <tr>
                                    <td>
                                        <strong>{{ $template->title }}</strong>
                                        @if ($template->description)
                                            <br><small class="text-muted">{{ Str::limit($template->description, 80) }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $template->questions_count }}</td>
                                    <td>
                                        @if ($template->is_public)
                                            <span class="badge bg-success">Public</span>
                                        @else
                                            <span class="badge bg-secondary">Team-only</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if (!$template->is_public)
                                            @if ($pendingTemplatePublishRequests->has($template->id))
                                                <span class="badge bg-warning text-dark">Request pending</span>
                                            @else
                                                <form method="POST" action="{{ route('user.assessment-templates.request-public', $template) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary">Request Public</button>
                                                </form>
                                            @endif
                                        @endif
                                        <form method="POST" action="{{ route('user.assessment-templates.destroy', $template) }}" class="d-inline"
                                              onsubmit="return confirm('Delete this template?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            {{-- Assessments --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <strong><i class="fas fa-clipboard-list"></i> Assessments ({{ $team->assessments->count() }})</strong>
                    @if (auth()->user()->planHasFeature('create-assessments'))
                        <a href="{{ route('user.assessments.create', ['team_id' => $team->id]) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-plus"></i> New
                        </a>
                    @endif
                </div>
                <div class="card-body p-0">
                    @if ($team->assessments->isEmpty())
                        <p class="text-muted p-3 mb-0">No assessments yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th class="text-center">Questions</th>
                                        <th class="text-center">Responses</th>
                                        <th>Created</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($team->assessments as $assessment)
                                    <tr>
                                        <td><strong>{{ $assessment->title }}</strong></td>
                                        <td>
                                            @if ($assessment->isDraft())
                                                <span class="badge bg-secondary">Draft</span>
                                            @elseif ($assessment->isActive())
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-dark">Closed</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $assessment->questions->count() }}</td>
                                        <td class="text-center">{{ $assessment->results->count() }}</td>
                                        <td class="text-muted small">{{ $assessment->created_at->format('d M Y') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('user.assessments.show', $assessment) }}" class="btn btn-sm btn-outline-primary">
                                                {{ $assessment->isClosed() ? 'Results' : 'View' }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@if ($maturity['assessment'])
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function () {
        const canvas = document.getElementById('teamMaturityRadar');
        if (!canvas) return;

        const categories = JSON.parse(canvas.dataset.categories);
        const scores = JSON.parse(canvas.dataset.scores);

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
                    r: { min: 0, max: 4, ticks: { stepSize: 1 }, pointLabels: { font: { size: 11 } } },
                },
                plugins: { legend: { display: false } },
            },
        });
    })();

    (function () {
        const canvas = document.getElementById('teamMaturityHistory');
        if (!canvas) return;

        const labels = JSON.parse(canvas.dataset.labels);
        const scores = JSON.parse(canvas.dataset.scores);

        new Chart(canvas, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Overall Maturity',
                    data: scores,
                    borderColor: 'rgba(13, 110, 253, 1)',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                }],
            },
            options: {
                scales: {
                    y: { min: 0, max: 4, ticks: { stepSize: 1 } },
                },
                plugins: { legend: { display: false } },
            },
        });
    })();
</script>
@endpush
@endif

@if($isOwner)
@push('scripts')
<script>
const searchUrl        = '{{ route('user.teams.members.search', $team->id) }}';
const memberRoles      = @json($memberRoles->map(fn($r) => ['value' => $r->slug, 'label' => $r->name, 'default' => $r->is_default]));
const defaultRole      = memberRoles.find(r => r.default)?.value ?? memberRoles[0]?.value ?? 'member';

const searchInput      = document.getElementById('memberSearch');
const searchResults    = document.getElementById('searchResults');
const searchSpinner    = document.getElementById('searchSpinner');
const inviteSection    = document.getElementById('inviteUnregisteredSection');
const notFoundEmail    = document.getElementById('notFoundEmail');
const inviteEmailInput = document.getElementById('inviteEmail');
const memberChips      = document.getElementById('memberChips');
const memberInputs     = document.getElementById('memberInputs');
const selectedWrap     = document.getElementById('selectedMembers');
const batchForm        = document.getElementById('batchInviteForm');
const inviteCountLabel = document.getElementById('inviteCountLabel');
const memberCount      = document.getElementById('memberCount');

// Map of id → {id, name, email, role}
const selected = {};
let debounceTimer;

searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    const q = searchInput.value.trim();
    if (q.length < 2) { searchResults.style.display = 'none'; return; }
    debounceTimer = setTimeout(() => doSearch(q), 300);
});

searchInput.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') searchResults.style.display = 'none';
});

document.addEventListener('click', (e) => {
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.style.display = 'none';
    }
});

async function doSearch(q) {
    searchSpinner.style.display = 'block';
    searchResults.style.display = 'none';
    try {
        const res   = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const users = await res.json();
        searchSpinner.style.display = 'none';
        renderResults(users, q);
    } catch {
        searchSpinner.style.display = 'none';
    }
}

function renderResults(users, q) {
    searchResults.innerHTML = '';

    users.forEach(user => {
        if (selected[user.id]) return; // already queued

        const item = document.createElement('div');
        item.className = 'px-3 py-2 border-bottom d-flex align-items-center justify-content-between';
        item.style.cursor = 'pointer';
        item.innerHTML = `
            <div>
                <strong>${escHtml(user.name)}</strong>
                <small class="text-muted d-block">${escHtml(user.email)}</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary">Add</button>
        `;
        item.addEventListener('click', () => addToQueue(user));
        item.addEventListener('mouseenter', () => item.classList.add('bg-light'));
        item.addEventListener('mouseleave', () => item.classList.remove('bg-light'));
        searchResults.appendChild(item);
    });

    // "Invite by email" option at the bottom
    const inviteOpt = document.createElement('div');
    inviteOpt.className = 'px-3 py-2 text-primary d-flex align-items-center gap-2';
    inviteOpt.style.cursor = 'pointer';
    inviteOpt.innerHTML = `<i class="fas fa-envelope"></i> <span>Send registration invite to <strong>${escHtml(q)}</strong></span>`;
    inviteOpt.addEventListener('click', () => showInviteSection(q));
    inviteOpt.addEventListener('mouseenter', () => inviteOpt.classList.add('bg-light'));
    inviteOpt.addEventListener('mouseleave', () => inviteOpt.classList.remove('bg-light'));
    searchResults.appendChild(inviteOpt);

    searchResults.style.display = 'block';
}

function addToQueue(user) {
    if (selected[user.id]) return;
    selected[user.id] = { ...user, role: defaultRole };
    searchResults.style.display = 'none';
    searchInput.value = '';
    inviteSection.style.display = 'none';
    renderChips();
    searchInput.focus();
}

function removeFromQueue(id) {
    delete selected[id];
    renderChips();
}

function renderChips() {
    memberChips.innerHTML  = '';
    memberInputs.innerHTML = '';
    const ids = Object.keys(selected);

    ids.forEach((id, index) => {
        const m = selected[id];

        const chip = document.createElement('div');
        chip.className = 'badge bg-light text-dark border d-flex align-items-center gap-2 py-2 px-3';
        chip.style.fontSize = '0.85rem';
        chip.innerHTML = `
            <span>${escHtml(m.name)}</span>
            <select class="form-select form-select-sm border-0 p-0 bg-transparent"
                    style="width:auto; font-size:0.8rem;"
                    onchange="selected[${id}].role = this.value; syncInputs()">
                ${memberRoles.map(r => `<option value="${r.value}" ${m.role === r.value ? 'selected' : ''}>${r.label}</option>`).join('')}
            </select>
            <button type="button" class="btn-close btn-close-sm" onclick="removeFromQueue(${id})"></button>
        `;
        memberChips.appendChild(chip);

        const inputId       = document.createElement('input');
        inputId.type        = 'hidden';
        inputId.name        = `members[${index}][id]`;
        inputId.value       = m.id;

        const inputRole     = document.createElement('input');
        inputRole.type      = 'hidden';
        inputRole.name      = `members[${index}][role]`;
        inputRole.value     = m.role;
        inputRole.id        = `role_input_${id}`;

        memberInputs.appendChild(inputId);
        memberInputs.appendChild(inputRole);
    });

    const count = ids.length;
    selectedWrap.style.display   = count > 0 ? 'block' : 'none';
    batchForm.style.display      = count > 0 ? 'block' : 'none';
    inviteCountLabel.textContent = count;
    memberCount.textContent      = `${count} to invite`;
    memberCount.style.display    = count > 0 ? 'inline' : 'none';
}

function syncInputs() {
    renderChips();
}

function showInviteSection(email) {
    searchResults.style.display = 'none';
    searchInput.value           = email;

    if (notFoundEmail)    notFoundEmail.textContent = email;
    if (inviteEmailInput) inviteEmailInput.value    = email;

    inviteSection.style.display = 'block';
}

function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endpush
@endif
