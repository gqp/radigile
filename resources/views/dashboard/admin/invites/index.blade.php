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
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <strong><span id="admin-invites-count">{{ $invites->total() }}</span> invite{{ $invites->total() === 1 ? '' : 's' }}</strong>
                        <x-search-box id="admin-invites-search" placeholder="Search code or email..." live />
                    </div>
                    <div id="admin-invites-results" data-live-url="{{ route('admin.invites.index') }}">
                        @include('dashboard.admin.invites._results', ['invites' => $invites])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        initLiveSearch('admin-invites-search', 'admin-invites-results', {
            onSwap: function () {
                const totalEl = document.getElementById('admin-invites-total-value');
                if (totalEl) {
                    document.getElementById('admin-invites-count').textContent = totalEl.textContent;
                }
            },
        });
    });
</script>
@endpush
