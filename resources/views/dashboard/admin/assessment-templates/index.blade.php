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

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="bi bi-bookmark-star"></i> Assessment Templates</h4>
        <div class="btn-group btn-group-sm" role="group" aria-label="Assessment templates view toggle">
            <button type="button" id="admin-assessment-templates-view-table-btn" class="btn btn-outline-secondary" onclick="setAdminAssessmentTemplatesView('table')">
                <i class="fas fa-list"></i> Table
            </button>
            <button type="button" id="admin-assessment-templates-view-card-btn" class="btn btn-outline-secondary" onclick="setAdminAssessmentTemplatesView('card')">
                <i class="fas fa-th-large"></i> Cards
            </button>
        </div>
    </div>

    {{-- Pending Publish Requests --}}
    @if ($pendingRequests->isNotEmpty())
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light">
            <strong><i class="fas fa-clock"></i> Pending Publish Requests ({{ $pendingRequests->count() }})</strong>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Template</th>
                            <th>Team</th>
                            <th>Requested By</th>
                            <th>Message</th>
                            <th>Requested</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendingRequests as $request)
                        <tr>
                            <td><strong>{{ $request->assessmentTemplate->title }}</strong></td>
                            <td>{{ $request->assessmentTemplate->team->name ?? '—' }}</td>
                            <td>
                                <strong>{{ $request->requester->name }}</strong>
                                <br><small class="text-muted">{{ $request->requester->email }}</small>
                            </td>
                            <td class="text-muted small">{{ $request->message ? Str::limit($request->message, 80) : '—' }}</td>
                            <td class="text-muted small">{{ $request->created_at->diffForHumans() }}</td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.assessment-template-publish-requests.approve', $request) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('admin.assessment-template-publish-requests.reject', $request) }}" class="d-inline"
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

    <div class="card shadow-sm mb-3">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong><span id="admin-assessment-templates-count">{{ $templates->total() }}</span> template{{ $templates->total() === 1 ? '' : 's' }}</strong>
            <x-search-box id="admin-assessment-templates-search" placeholder="Search title, team, or creator..." live />
        </div>
    </div>

    <div id="admin-assessment-templates-results" data-live-url="{{ route('admin.assessment-templates.index') }}">
        @include('dashboard.admin.assessment-templates._results', ['templates' => $templates])
    </div>
</div>
@endsection

@push('scripts')
<script>
    function setAdminAssessmentTemplatesView(mode) {
        document.querySelectorAll('.admin-assessment-templates-view-table').forEach(el => el.classList.toggle('d-none', mode !== 'table'));
        document.querySelectorAll('.admin-assessment-templates-view-cards').forEach(el => el.classList.toggle('d-none', mode !== 'card'));
        document.getElementById('admin-assessment-templates-view-table-btn').classList.toggle('active', mode === 'table');
        document.getElementById('admin-assessment-templates-view-card-btn').classList.toggle('active', mode === 'card');
        localStorage.setItem('adminAssessmentTemplatesViewMode', mode);
    }

    document.addEventListener('DOMContentLoaded', function () {
        setAdminAssessmentTemplatesView(localStorage.getItem('adminAssessmentTemplatesViewMode') === 'card' ? 'card' : 'table');

        initLiveSearch('admin-assessment-templates-search', 'admin-assessment-templates-results', {
            onSwap: function () {
                setAdminAssessmentTemplatesView(localStorage.getItem('adminAssessmentTemplatesViewMode') === 'card' ? 'card' : 'table');
                const totalEl = document.getElementById('admin-assessment-templates-total-value');
                if (totalEl) {
                    document.getElementById('admin-assessment-templates-count').textContent = totalEl.textContent;
                }
            },
        });
    });
</script>
@endpush
