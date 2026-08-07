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

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-compass"></i> Browse Teams</h4>
        <div class="d-flex align-items-center gap-2">
            <div class="btn-group btn-group-sm" role="group" aria-label="Browse teams view toggle">
                <button type="button" id="browse-teams-view-table-btn" class="btn btn-outline-secondary" onclick="setBrowseTeamsView('table')">
                    <i class="fas fa-list"></i> Table
                </button>
                <button type="button" id="browse-teams-view-card-btn" class="btn btn-outline-secondary" onclick="setBrowseTeamsView('card')">
                    <i class="fas fa-th-large"></i> Cards
                </button>
            </div>
            <a href="{{ route('user.teams.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-inbox"></i> My Requests
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong><span id="browse-teams-count">{{ $teams->total() }}</span> team{{ $teams->total() === 1 ? '' : 's' }} open to join requests</strong>
            <x-search-box id="browse-teams-search" placeholder="Search teams..." live />
        </div>
        <div class="card-body p-0" id="browse-teams-results" data-live-url="{{ route('user.teams.browse') }}">
            @include('dashboard.user.teams._browse-results', ['teams' => $teams, 'pendingTeamIds' => $pendingTeamIds])
        </div>
    </div>

</div>

<style>
    .browse-team-card { transition: transform .15s ease, box-shadow .15s ease; }
    .browse-team-card:hover { transform: translateY(-4px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.15) !important; }
</style>
@endsection

@push('scripts')
<script>
    function setBrowseTeamsView(mode) {
        document.querySelectorAll('.browse-teams-view-table').forEach(el => el.classList.toggle('d-none', mode !== 'table'));
        document.querySelectorAll('.browse-teams-view-cards').forEach(el => el.classList.toggle('d-none', mode !== 'card'));
        document.getElementById('browse-teams-view-table-btn').classList.toggle('active', mode === 'table');
        document.getElementById('browse-teams-view-card-btn').classList.toggle('active', mode === 'card');
        localStorage.setItem('browseTeamsViewMode', mode);
    }

    document.addEventListener('DOMContentLoaded', function () {
        setBrowseTeamsView(localStorage.getItem('browseTeamsViewMode') === 'card' ? 'card' : 'table');

        initLiveSearch('browse-teams-search', 'browse-teams-results', {
            onSwap: function () {
                setBrowseTeamsView(localStorage.getItem('browseTeamsViewMode') === 'card' ? 'card' : 'table');
                const totalEl = document.getElementById('browse-teams-total-value');
                if (totalEl) {
                    document.getElementById('browse-teams-count').textContent = totalEl.textContent;
                }
            },
        });
    });
</script>
@endpush
