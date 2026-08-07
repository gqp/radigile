@extends('layouts.app')

@section('content')
    @include('layouts.partials.navbar')
    <div class="container py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-layer-group"></i> Manage Teams</h4>
            <div class="d-flex align-items-center gap-2">
                <div class="btn-group btn-group-sm" role="group" aria-label="Teams view toggle">
                    <button type="button" id="admin-teams-view-table-btn" class="btn btn-outline-secondary" onclick="setAdminTeamsView('table')">
                        <i class="fas fa-list"></i> Table
                    </button>
                    <button type="button" id="admin-teams-view-card-btn" class="btn btn-outline-secondary" onclick="setAdminTeamsView('card')">
                        <i class="fas fa-th-large"></i> Cards
                    </button>
                </div>
                <a href="{{ route('admin.teams.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Create New Team
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                <strong><span id="admin-teams-count">{{ $teams->total() }}</span> team{{ $teams->total() === 1 ? '' : 's' }}</strong>
                <x-search-box id="admin-teams-search" placeholder="Search teams or owner..." live />
            </div>
            <div class="card-body p-0" id="admin-teams-results" data-live-url="{{ route('admin.teams.index') }}">
                @include('dashboard.admin.teams._results', ['teams' => $teams])
            </div>
        </div>

    </div>

    <style>
        .admin-team-card { transition: transform .15s ease, box-shadow .15s ease; }
        .admin-team-card:hover { transform: translateY(-4px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.15) !important; }
    </style>
@endsection

@push('scripts')
<script>
    function confirmDeleteTeam() {
        return confirm('Are you sure you want to delete this team? This action cannot be undone.');
    }

    function setAdminTeamsView(mode) {
        document.querySelectorAll('.admin-teams-view-table').forEach(el => el.classList.toggle('d-none', mode !== 'table'));
        document.querySelectorAll('.admin-teams-view-cards').forEach(el => el.classList.toggle('d-none', mode !== 'card'));
        document.getElementById('admin-teams-view-table-btn').classList.toggle('active', mode === 'table');
        document.getElementById('admin-teams-view-card-btn').classList.toggle('active', mode === 'card');
        localStorage.setItem('adminTeamsViewMode', mode);
    }

    document.addEventListener('DOMContentLoaded', function () {
        setAdminTeamsView(localStorage.getItem('adminTeamsViewMode') === 'card' ? 'card' : 'table');

        initLiveSearch('admin-teams-search', 'admin-teams-results', {
            onSwap: function () {
                setAdminTeamsView(localStorage.getItem('adminTeamsViewMode') === 'card' ? 'card' : 'table');
                const totalEl = document.getElementById('admin-teams-total-value');
                if (totalEl) {
                    document.getElementById('admin-teams-count').textContent = totalEl.textContent;
                }
            },
        });
    });
</script>
@endpush
