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
    <!-- Include Navbar -->
    @include('layouts.admin.navbar')
    <div class="app-container">
        <div class="container mt-5">
            {{-- Page Header --}}
            @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session()->has('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Manage Invites</h1>
                {{-- Current Status --}}
                <div class="alert alert-{{ \App\Models\Setting::get('invite_only') ? 'success' : 'danger' }}">
                    <strong>Invitation System is
                        currently {{ \App\Models\Setting::get('invite_only') ? 'Enabled' : 'Disabled' }}.</strong>
                    You can toggle it from the <a href="{{ route('admin.settings') }}">Settings Page</a>.
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
                            <input type="email" id="email" name="email[]" class="form-control"
                                   placeholder="Enter email(s)" multiple required>
                            <small class="text-muted">Enter one or multiple email addresses separated by commas.</small>
                        </div>

                        {{-- Max Uses --}}
                        <div class="mb-3">
                            <label for="max_uses" class="form-label">Maximum Uses</label>
                            <input type="number" id="max_uses" name="max_uses" class="form-control" value="1" required>
                        </div>

                        {{-- Expiration Date --}}
                        <div class="mb-3">
                            <label for="expires_at" class="form-label">Expiration Date</label>
                            <input type="datetime-local" id="expires_at" name="expires_at" class="form-control">
                            <small class="text-muted">Optional: Leave blank for no expiration date.</small>
                        </div>

                        <button type="submit" class="btn btn-primary">Send Invite</button>
                    </form>
                </div>
            </div>

            {{-- All Invites Table --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">All Invites</h5>
                </div>
                <div class="card-body">
                    @if($invites->isEmpty())
                        <p>No invites found.</p>
                    @else
                        <table class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Invited User</th>
                                <th>Created By</th>
                                <th>Max Uses</th>
                                <th>Times Used</th>
                                <th>Active</th>
                                <th>Expires At</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($invites as $invite)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $invite->code }}</td>
                                    <td>
                                        @if($invite->invitedUser)
                                            {{-- Registered User --}}
                                            <strong>{{ $invite->invitedUser->name }}</strong>
                                            ({{ $invite->invitedUser->email }})
                                        @else
                                            {{-- Non-registered User --}}
                                            <span class="text-danger">
                                                    Non-registered User
                                                </span>
                                            ({{ $invite->email }})
                                        @endif
                                    </td>
                                    <td>{{ $invite->creator->name ?? 'N/A' }}</td>
                                    <td>{{ $invite->max_uses }}</td>
                                    <td>{{ $invite->times_used }}</td>
                                    <td>
                                            <span class="badge bg-{{ $invite->is_active ? 'success' : 'danger' }}">
                                                {{ $invite->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                    </td>
                                    <td>{{ $invite->expires_at ? $invite->expires_at->format('Y-m-d H:i:s') : 'Does not expire' }}</td>
                                    <td>
                                        @if($invite->is_active)
                                            <form method="POST" action="{{ route('admin.invites.disable', $invite->id) }}" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-danger">Disable</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.invites.enable', $invite->id) }}" class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-sm btn-success">Enable</button>
                                            </form>
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
    </div>
@endsection
