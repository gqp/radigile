@extends('layouts.app')

@section('content')
    <!-- Include Navbar -->
    @include('layouts.partials.navbar')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    {{-- Card Header --}}
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-clipboard-data"></i> Manage Subscriptions</h4>
                        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-plus-circle"></i> Create Subscription
                        </a>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <strong><span id="admin-subscriptions-count">{{ $subscriptions->total() }}</span> subscription{{ $subscriptions->total() === 1 ? '' : 's' }}</strong>
                            <x-search-box id="admin-subscriptions-search" placeholder="Search user, email, or plan..." live />
                        </div>
                        <div id="admin-subscriptions-results" data-live-url="{{ route('admin.subscriptions.index') }}">
                            @include('dashboard.admin.subscriptions._results', ['subscriptions' => $subscriptions])
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initLiveSearch('admin-subscriptions-search', 'admin-subscriptions-results', {
            onSwap: function () {
                const totalEl = document.getElementById('admin-subscriptions-total-value');
                if (totalEl) {
                    document.getElementById('admin-subscriptions-count').textContent = totalEl.textContent;
                }
            },
        });
    });
</script>
@endpush
