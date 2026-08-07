@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-clipboard-list"></i> All Assessments</h4>
        <div class="d-flex align-items-center gap-2">
            <div class="btn-group btn-group-sm" role="group" aria-label="Assessments view toggle">
                <button type="button" id="admin-assessments-view-table-btn" class="btn btn-outline-secondary" onclick="setAdminAssessmentsView('table')">
                    <i class="fas fa-list"></i> Table
                </button>
                <button type="button" id="admin-assessments-view-card-btn" class="btn btn-outline-secondary" onclick="setAdminAssessmentsView('card')">
                    <i class="fas fa-th-large"></i> Cards
                </button>
            </div>
            <a href="{{ route('user.assessments.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle"></i> Create Assessment
            </a>
        </div>
    </div>

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong><span id="admin-assessments-count">{{ $assessments->total() }}</span> assessment{{ $assessments->total() === 1 ? '' : 's' }}</strong>
            <x-search-box id="admin-assessments-search" placeholder="Search title, team, or creator..." live />
        </div>
    </div>

    <div id="admin-assessments-results" data-live-url="{{ route('admin.assessments.index') }}">
        @include('dashboard.admin.assessments._results', ['assessments' => $assessments])
    </div>
</div>

<style>
    .admin-assessment-card { transition: transform .15s ease, box-shadow .15s ease; }
    .admin-assessment-card:hover { transform: translateY(-4px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.15) !important; }
</style>
@endsection

@push('scripts')
<script>
    function setAdminAssessmentsView(mode) {
        document.querySelectorAll('.admin-assessments-view-table').forEach(el => el.classList.toggle('d-none', mode !== 'table'));
        document.querySelectorAll('.admin-assessments-view-cards').forEach(el => el.classList.toggle('d-none', mode !== 'card'));
        document.getElementById('admin-assessments-view-table-btn').classList.toggle('active', mode === 'table');
        document.getElementById('admin-assessments-view-card-btn').classList.toggle('active', mode === 'card');
        localStorage.setItem('adminAssessmentsViewMode', mode);
    }

    document.addEventListener('DOMContentLoaded', function () {
        setAdminAssessmentsView(localStorage.getItem('adminAssessmentsViewMode') === 'card' ? 'card' : 'table');

        initLiveSearch('admin-assessments-search', 'admin-assessments-results', {
            onSwap: function () {
                setAdminAssessmentsView(localStorage.getItem('adminAssessmentsViewMode') === 'card' ? 'card' : 'table');
                const totalEl = document.getElementById('admin-assessments-total-value');
                if (totalEl) {
                    document.getElementById('admin-assessments-count').textContent = totalEl.textContent;
                }
            },
        });
    });
</script>
@endpush
