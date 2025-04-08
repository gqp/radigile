@extends('layouts.app')

@section('content')
    <style>
        /* Main container for the toggle */
        .switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }

        /* Hide the checkbox */
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        /* Create the slider */
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 26px; /* Rounded slider */
        }

        /* Position the circle inside */
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }

        /* Change background to highlight when checked */
        input:checked + .slider {
            background-color: #4caf50;
        }

        /* Move the circle when checked */
        input:checked + .slider:before {
            transform: translateX(24px);
        }

        /* Custom styling for the Update button */
        .btn-update {
            display: inline-block;
            margin-top: 20px;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }

        .btn-update:hover {
            background-color: #0056b3;
        }
    </style>

    <div class="container mt-5">
        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Manage Invites</h1>
            <div>
                <form action="{{ route('admin.invites.toggle') }}" method="POST" id="toggleInviteForm">
                    @csrf
                    <div>
                        <label class="form-switch">
                            <input type="checkbox" name="status" value="1" id="inviteOnlyToggle"
                                   onchange="document.getElementById('toggleInviteForm').submit()"
                                {{ $inviteOnly ? 'checked' : '' }}> <!-- Check the toggle if invite-only is enabled -->
                            <i></i>
                            Invite Only Mode
                        </label>
                    </div>
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
