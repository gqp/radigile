@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container py-4">

    {{-- Flash messages --}}
    @foreach (['success', 'error', 'info'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> Assessments</h4>
        <div class="d-flex align-items-center gap-2">
            <div class="btn-group btn-group-sm" role="group" aria-label="Assessments view toggle">
                <button type="button" id="assessments-view-table-btn" class="btn btn-outline-secondary" onclick="setAssessmentsView('table')">
                    <i class="fas fa-list"></i> Table
                </button>
                <button type="button" id="assessments-view-card-btn" class="btn btn-outline-secondary" onclick="setAssessmentsView('card')">
                    <i class="fas fa-th-large"></i> Cards
                </button>
            </div>
            <a href="{{ route('user.assessments.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Create Assessment
            </a>
        </div>
    </div>

    {{-- My Assessments (as owner) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <strong>My Assessments ({{ $myAssessments->count() }})</strong>
        </div>
        <div class="card-body p-0">
            @if ($myAssessments->isEmpty())
                <p class="text-muted p-3 mb-0">You haven't created any assessments yet.</p>
            @else
                {{-- Table view --}}
                <div class="table-responsive assessments-view-table">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Team</th>
                                <th class="text-center">Maturity</th>
                                <th>Status</th>
                                <th>Questions</th>
                                <th>Responses</th>
                                <th>Created</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($myAssessments as $assessment)
                            <tr>
                                <td><strong>{{ $assessment->title }}</strong></td>
                                <td>{{ $assessment->team->name }}</td>
                                <td class="text-center">
                                    @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 44])
                                </td>
                                <td>
                                    @if ($assessment->isDraft())
                                        <span class="badge bg-secondary">Draft</span>
                                    @elseif ($assessment->isActive())
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-dark">Closed</span>
                                    @endif
                                </td>
                                <td>{{ $assessment->questions->count() }}</td>
                                <td>{{ $assessment->responses->pluck('user_id')->unique()->count() }}</td>
                                <td>{{ $assessment->created_at->format('d M Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('user.assessments.show', $assessment) }}" class="btn btn-sm btn-outline-primary">Manage</a>
                                    @if ($assessment->isClosed())
                                        <a href="{{ route('user.assessments.results', $assessment) }}" class="btn btn-sm btn-outline-dark">Results</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Card view --}}
                <div class="row g-4 p-3 assessments-view-cards d-none">
                    @foreach ($myAssessments as $assessment)
                    @php
                        $headerClass = $assessment->isDraft() ? 'bg-secondary' : ($assessment->isActive() ? 'bg-success' : 'bg-dark');
                    @endphp
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 shadow border-0 assessment-card">
                            <div class="card-header {{ $headerClass }} text-white d-flex align-items-center gap-2 py-3">
                                <i class="fas fa-clipboard-list fa-lg"></i>
                                <h5 class="mb-0 fw-bold text-truncate">{{ $assessment->title }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center mb-3">
                                    @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 110])
                                </div>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <span class="badge bg-light text-dark border px-3 py-2">
                                        <i class="fas fa-users me-1"></i> {{ $assessment->team->name }}
                                    </span>
                                    <span class="badge {{ $headerClass }} px-3 py-2">
                                        @if ($assessment->isDraft()) Draft @elseif ($assessment->isActive()) Active @else Closed @endif
                                    </span>
                                </div>
                                <p class="mb-1 small text-muted"><i class="fas fa-question-circle me-1"></i> {{ $assessment->questions->count() }} questions</p>
                                <p class="mb-1 small text-muted"><i class="fas fa-reply me-1"></i> {{ $assessment->responses->pluck('user_id')->unique()->count() }} responses</p>
                                <p class="text-muted small mb-0"><i class="fas fa-calendar me-1"></i> Created {{ $assessment->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 d-flex gap-2 pb-3">
                                <a href="{{ route('user.assessments.show', $assessment) }}" class="btn btn-primary flex-fill">Manage</a>
                                @if ($assessment->isClosed())
                                    <a href="{{ route('user.assessments.results', $assessment) }}" class="btn btn-outline-dark flex-fill">Results</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Pending (need to take) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <strong>Pending — Awaiting Your Response ({{ $pendingAssessments->count() }})</strong>
        </div>
        <div class="card-body p-0">
            @if ($pendingAssessments->isEmpty())
                <p class="text-muted p-3 mb-0">Nothing waiting for your response.</p>
            @else
                {{-- Table view --}}
                <div class="table-responsive assessments-view-table">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Team</th>
                                <th class="text-center">Maturity</th>
                                <th>Questions</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pendingAssessments as $assessment)
                            <tr>
                                <td><strong>{{ $assessment->title }}</strong></td>
                                <td>{{ $assessment->team->name }}</td>
                                <td class="text-center">
                                    @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 44])
                                </td>
                                <td>{{ $assessment->questions->count() }}</td>
                                <td class="text-end">
                                    <a href="{{ route('user.assessments.take', $assessment) }}" class="btn btn-sm btn-success">Take Assessment</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Card view --}}
                <div class="row g-4 p-3 assessments-view-cards d-none">
                    @foreach ($pendingAssessments as $assessment)
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 shadow border-0 assessment-card">
                            <div class="card-header bg-warning text-dark d-flex align-items-center gap-2 py-3">
                                <i class="fas fa-hourglass-half fa-lg"></i>
                                <h5 class="mb-0 fw-bold text-truncate">{{ $assessment->title }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center mb-3">
                                    @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 110])
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-2 mb-3 d-inline-block">
                                    <i class="fas fa-users me-1"></i> {{ $assessment->team->name }}
                                </span>
                                <p class="text-muted small mb-0"><i class="fas fa-question-circle me-1"></i> {{ $assessment->questions->count() }} questions</p>
                            </div>
                            <div class="card-footer bg-white border-top-0 pb-3">
                                <a href="{{ route('user.assessments.take', $assessment) }}" class="btn btn-success w-100">Take Assessment</a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Completed (taken, waiting to close) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <strong>Completed — Awaiting Results ({{ $completedAssessments->count() }})</strong>
        </div>
        <div class="card-body p-0">
            @if ($completedAssessments->isEmpty())
                <p class="text-muted p-3 mb-0">No completed assessments waiting on the owner to close.</p>
            @else
                {{-- Table view --}}
                <div class="table-responsive assessments-view-table">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Team</th>
                                <th class="text-center">Maturity</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($completedAssessments as $assessment)
                            <tr>
                                <td><strong>{{ $assessment->title }}</strong></td>
                                <td>{{ $assessment->team->name }}</td>
                                <td class="text-center">
                                    @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 44])
                                </td>
                                <td><span class="badge bg-info text-dark">Submitted — waiting for owner to close</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Card view --}}
                <div class="row g-4 p-3 assessments-view-cards d-none">
                    @foreach ($completedAssessments as $assessment)
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 shadow border-0 assessment-card">
                            <div class="card-header bg-info text-dark d-flex align-items-center gap-2 py-3">
                                <i class="fas fa-check-circle fa-lg"></i>
                                <h5 class="mb-0 fw-bold text-truncate">{{ $assessment->title }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center mb-3">
                                    @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 110])
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-2 mb-3 d-inline-block">
                                    <i class="fas fa-users me-1"></i> {{ $assessment->team->name }}
                                </span>
                                <p class="text-muted small mb-0">Submitted — waiting for owner to close</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Closed (results available) --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <strong>Closed — Results Available ({{ $closedAssessments->count() }})</strong>
        </div>
        <div class="card-body p-0">
            @if ($closedAssessments->isEmpty())
                <p class="text-muted p-3 mb-0">No closed assessments yet.</p>
            @else
                {{-- Table view --}}
                <div class="table-responsive assessments-view-table">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Team</th>
                                <th class="text-center">Maturity</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($closedAssessments as $assessment)
                            <tr>
                                <td><strong>{{ $assessment->title }}</strong></td>
                                <td>{{ $assessment->team->name }}</td>
                                <td class="text-center">
                                    @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 44])
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('user.assessments.results', $assessment) }}" class="btn btn-sm btn-outline-dark">View Results</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Card view --}}
                <div class="row g-4 p-3 assessments-view-cards d-none">
                    @foreach ($closedAssessments as $assessment)
                    <div class="col-md-6 col-xl-4">
                        <div class="card h-100 shadow border-0 assessment-card">
                            <div class="card-header bg-dark text-white d-flex align-items-center gap-2 py-3">
                                <i class="fas fa-lock fa-lg"></i>
                                <h5 class="mb-0 fw-bold text-truncate">{{ $assessment->title }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-center mb-3">
                                    @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 110])
                                </div>
                                <span class="badge bg-light text-dark border px-3 py-2 mb-3 d-inline-block">
                                    <i class="fas fa-users me-1"></i> {{ $assessment->team->name }}
                                </span>
                            </div>
                            <div class="card-footer bg-white border-top-0 pb-3">
                                <a href="{{ route('user.assessments.results', $assessment) }}" class="btn btn-outline-dark w-100">View Results</a>
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
    .assessment-card { transition: transform .15s ease, box-shadow .15s ease; }
    .assessment-card:hover { transform: translateY(-4px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.15) !important; }
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    function setAssessmentsView(mode) {
        document.querySelectorAll('.assessments-view-table').forEach(el => el.classList.toggle('d-none', mode !== 'table'));
        document.querySelectorAll('.assessments-view-cards').forEach(el => el.classList.toggle('d-none', mode !== 'card'));
        document.getElementById('assessments-view-table-btn').classList.toggle('active', mode === 'table');
        document.getElementById('assessments-view-card-btn').classList.toggle('active', mode === 'card');
        localStorage.setItem('assessmentsViewMode', mode);
    }

    document.addEventListener('DOMContentLoaded', function () {
        setAssessmentsView(localStorage.getItem('assessmentsViewMode') === 'card' ? 'card' : 'table');
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
