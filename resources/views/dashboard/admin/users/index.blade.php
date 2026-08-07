@extends('layouts.app')

@section('content')
    @include('layouts.partials.navbar')
    <div class="container py-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-users"></i> User Management</h4>
            <div class="d-flex align-items-center gap-2">
                <div class="btn-group btn-group-sm" role="group" aria-label="Users view toggle">
                    <button type="button" id="users-view-table-btn" class="btn btn-outline-secondary" onclick="setUsersView('table')">
                        <i class="fas fa-list"></i> Table
                    </button>
                    <button type="button" id="users-view-card-btn" class="btn btn-outline-secondary" onclick="setUsersView('card')">
                        <i class="fas fa-th-large"></i> Cards
                    </button>
                </div>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus-circle"></i> Create User
                </a>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
                <strong><span id="users-count">{{ $users->total() }}</span> user{{ $users->total() === 1 ? '' : 's' }}</strong>
                <x-search-box id="users-search" placeholder="Search name or email..." live />
            </div>
            <div class="card-body p-0" id="users-results" data-live-url="{{ route('admin.users.index') }}">
                @include('dashboard.admin.users._results', ['users' => $users])
            </div>
        </div>

    </div>

    <style>
        .user-card { transition: transform .15s ease, box-shadow .15s ease; }
        .user-card:hover { transform: translateY(-4px); box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.15) !important; }
    </style>
@endsection

@push('scripts')
<script>
    function setUsersView(mode) {
        document.querySelectorAll('.users-view-table').forEach(el => el.classList.toggle('d-none', mode !== 'table'));
        document.querySelectorAll('.users-view-cards').forEach(el => el.classList.toggle('d-none', mode !== 'card'));
        document.getElementById('users-view-table-btn').classList.toggle('active', mode === 'table');
        document.getElementById('users-view-card-btn').classList.toggle('active', mode === 'card');
        localStorage.setItem('usersViewMode', mode);
    }

    document.addEventListener('DOMContentLoaded', function () {
        setUsersView(localStorage.getItem('usersViewMode') === 'card' ? 'card' : 'table');

        initLiveSearch('users-search', 'users-results', {
            onSwap: function () {
                setUsersView(localStorage.getItem('usersViewMode') === 'card' ? 'card' : 'table');
                const totalEl = document.getElementById('users-total-value');
                if (totalEl) {
                    document.getElementById('users-count').textContent = totalEl.textContent;
                }
            },
        });
    });
</script>
@endpush
