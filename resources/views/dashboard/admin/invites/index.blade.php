@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Manage Invites</h1>
            <div>
                <a href="#" class="btn btn-sm btn-outline-secondary"
                   onclick="event.preventDefault(); document.getElementById('toggle-form').submit();">
                    Toggle Invite Only:
                    <span class="badge {{ $inviteOnly ? 'bg-success' : 'bg-secondary' }}">
                        {{ $inviteOnly ? 'ON' : 'OFF' }}
                    </span>
                </a>
                <form id="toggle-form" action="{{ route('admin.invites.toggle') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>

        {{-- Generate Invite Form --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Generate and Send Invite</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.invites.create') }}">
                    @csrf

                    {{-- Email Input --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Recipient Email(s)</label>
                        <input type="email" id="email" name="emails[]" class="form-control" multiple required>
                    </div>
                    <button type="submit" class="btn btn-primary">Generate Invite</button>
                </form>
            </div>
        </div>

        {{-- Invite List --}}
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">All Invites</h5>
            </div>
            <div class="card-body">
                @if ($invites->isEmpty())
                    <p class="text-muted">No invites available.</p>
                @else
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Created By</th>
                            <th>Invited User ID</th> {{-- Added Column --}}
                            <th>Times Used</th>
                            <th>Max Uses</th>
                            <th>Status</th>
                            <th>Expires At</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($invites as $index => $invite)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $invite->code }}</td>
                                <td>{{ $invite->creator->name }}</td>
                                <td>
                                    {{ $invite->invited_user_id ?? 'N/A' }} {{-- Display invited_user_id --}}
                                </td>
                                <td>{{ $invite->times_used }}</td>
                                <td>{{ $invite->max_uses }}</td>
                                <td>
                                    @if ($invite->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $invite->expires_at ? $invite->expires_at->format('Y-m-d H:i') : 'No Expiry' }}</td>
                                <td>
                                    <!-- Actions Here -->
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection
