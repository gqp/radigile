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

        {{-- Invite Form --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Generate and Send Invite</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.invites.create') }}">
                    @csrf

                    {{-- Email Input --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email (for non-registered users)</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter email for new user">
                    </div>

                    {{-- User Selection --}}
                    <div class="mb-3">
                        <label for="user_id" class="form-label">Registered User</label>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">Select a registered user (optional)</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Invite Details --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="max_uses" class="form-label">Max Uses</label>
                            <input type="number" name="max_uses" id="max_uses" class="form-control" value="1" min="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="expires_at" class="form-label">Expires At</label>
                            <input type="date" name="expires_at" id="expires_at" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">Generate and Send Invite</button>
                </form>
            </div>
        </div>

        {{-- Existing Invites --}}
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Existing Invites</h5>
            </div>
            <div class="card-body">
                @if($invites->isEmpty())
                    <div class="text-center">
                        <p class="text-muted">No invites have been created yet.</p>
                    </div>
                @else
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr>
                            <th>Code</th>
                            <th>Creator</th>
                            <th>Times Used</th>
                            <th>Max Uses</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invites as $invite)
                            <tr>
                                <td>{{ $invite->code }}</td>
                                <td>{{ $invite->creator->name }}</td>
                                <td>{{ $invite->times_used }}/{{ $invite->max_uses }}</td>
                                <td>
                                        <span class="badge {{ $invite->max_uses > $invite->times_used ? 'bg-success' : 'bg-danger' }}">
                                            {{ $invite->times_used < $invite->max_uses ? 'Available' : 'Used Up' }}
                                        </span>
                                </td>
                                <td>
                                        <span class="badge {{ $invite->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $invite->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                </td>
                                <td>
                                    @if($invite->is_active)
                                        <form method="POST" action="{{ route('admin.invites.disable', $invite->id) }}" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                Disable
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled>Disabled</button>
                                    @endif
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
