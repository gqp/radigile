@extends('layouts.app')

@section('content')
<div class="app-container">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                {{-- Profile Card --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-person-fill"></i> Admin Profile
                        </h4>
                        <span>Welcome, {{ Auth::user()->name }}</span>
                    </div>

                    <div class="card-body">
                        <h5 class="text-secondary">
                            <i class="bi bi-card-checklist"></i> Profile Details
                        </h5>
                        <hr>

                        {{-- Admin Profile Section --}}
                        <div class="mb-4">
                            <strong>Member Since:</strong>
                            <span>{{ Auth::user()->created_at->isoFormat('MMMM D, YYYY [at] h:mm A') }}</span>
                        </div>

                        <h5 class="text-secondary">
                            <i class="bi bi-lock-fill"></i> Update Password
                        </h5>
                        <hr>

                        {{-- Update Password Form --}}
                        <form action="{{ route('admin.updatePassword') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input
                                    type="password"
                                    name="current_password"
                                    id="current_password"
                                    class="form-control @error('current_password') is-invalid @enderror"
                                    required
                                >
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    required
                                >
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    required
                                >
                                @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </form>

                        {{-- Success or Error Messages --}}
                        @if(session('success'))
                            <div class="alert alert-success mt-3">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger mt-3">
                                {{ session('error') }}
                            </div>
                        @endif

                        <h5 class="text-secondary mt-4">
                            <i class="bi bi-tools"></i> Coming Soon!
                        </h5>
                        <p class="text-muted mb-0">
                            Add profile-related settings here in the future...
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    <!-- Custom Styles for Profile Page -->
    <style>
        .card-header h4 {
            font-size: 1.5rem;
        }

        /* Adjusting any necessary form button or layout styles */
        button.btn-primary {
            background-color: #663399;
            border: none;
        }

        button.btn-primary:hover {
            background-color: #552b81;
        }
    </style>
@endpush
