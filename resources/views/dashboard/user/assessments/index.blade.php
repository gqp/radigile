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
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong><span id="my-assessments-count">{{ $myAssessments->total() }}</span> My Assessments</strong>
            <x-search-box id="my-assessments-search" param="my_search" page-param="my_page" placeholder="Search my assessments..." live />
        </div>
        <div class="card-body p-0" id="my-assessments-results" data-live-url="{{ route('user.assessments.index', ['section' => 'my']) }}">
            @include('dashboard.user.assessments._my-results', ['myAssessments' => $myAssessments, 'maturityByTeamId' => $maturityByTeamId])
        </div>
    </div>

    {{-- Pending (need to take) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong><span id="pending-assessments-count">{{ $pendingAssessments->total() }}</span> Pending — Awaiting Your Response</strong>
            <x-search-box id="pending-assessments-search" param="pending_search" page-param="pending_page" placeholder="Search pending..." live />
        </div>
        <div class="card-body p-0" id="pending-assessments-results" data-live-url="{{ route('user.assessments.index', ['section' => 'pending']) }}">
            @include('dashboard.user.assessments._pending-results', ['pendingAssessments' => $pendingAssessments, 'maturityByTeamId' => $maturityByTeamId])
        </div>
    </div>

    {{-- Completed (taken, waiting to close) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong><span id="completed-assessments-count">{{ $completedAssessments->total() }}</span> Completed — Awaiting Results</strong>
            <x-search-box id="completed-assessments-search" param="completed_search" page-param="completed_page" placeholder="Search completed..." live />
        </div>
        <div class="card-body p-0" id="completed-assessments-results" data-live-url="{{ route('user.assessments.index', ['section' => 'completed']) }}">
            @include('dashboard.user.assessments._completed-results', ['completedAssessments' => $completedAssessments, 'maturityByTeamId' => $maturityByTeamId])
        </div>
    </div>

    {{-- Closed (results available) --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong><span id="closed-assessments-count">{{ $closedAssessments->total() }}</span> Closed — Results Available</strong>
            <x-search-box id="closed-assessments-search" param="closed_search" page-param="closed_page" placeholder="Search closed..." live />
        </div>
        <div class="card-body p-0" id="closed-assessments-results" data-live-url="{{ route('user.assessments.index', ['section' => 'closed']) }}">
            @include('dashboard.user.assessments._closed-results', ['closedAssessments' => $closedAssessments, 'maturityByTeamId' => $maturityByTeamId])
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
        setAssessmentsView(localStorage.getItem('assessmentsViewMode') === 'card' ? 'card' : 'table');
        initMaturityRadars(document);

        [
            ['my-assessments-search', 'my-assessments-results', 'my-assessments-total-value', 'my-assessments-count'],
            ['pending-assessments-search', 'pending-assessments-results', 'pending-assessments-total-value', 'pending-assessments-count'],
            ['completed-assessments-search', 'completed-assessments-results', 'completed-assessments-total-value', 'completed-assessments-count'],
            ['closed-assessments-search', 'closed-assessments-results', 'closed-assessments-total-value', 'closed-assessments-count'],
        ].forEach(function ([searchId, resultsId, totalValueId, countId]) {
            initLiveSearch(searchId, resultsId, {
                onSwap: function () {
                    setAssessmentsView(localStorage.getItem('assessmentsViewMode') === 'card' ? 'card' : 'table');
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
