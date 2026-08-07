@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-satellite-dish"></i> Team Maturity Report</h4>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong><span id="admin-team-maturity-count">{{ $maturity->total() }}</span> team{{ $maturity->total() === 1 ? '' : 's' }}</strong>
            <x-search-box id="admin-team-maturity-search" placeholder="Search teams or owner..." live />
        </div>
        <div id="admin-team-maturity-results" data-live-url="{{ route('admin.team-maturity.index') }}">
            @include('dashboard.admin.team-maturity._results', ['maturity' => $maturity])
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initLiveSearch('admin-team-maturity-search', 'admin-team-maturity-results', {
            onSwap: function () {
                const totalEl = document.getElementById('admin-team-maturity-total-value');
                if (totalEl) {
                    document.getElementById('admin-team-maturity-count').textContent = totalEl.textContent;
                }
            },
        });
    });
</script>
@endpush
