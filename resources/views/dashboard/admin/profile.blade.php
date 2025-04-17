@extends('layouts.admin')

@section('content')
    <!-- Include Navbar -->
    @include('layouts.admin.navbar')
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
                            {{-- Display Success Message --}}
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif

                            {{-- Display Error Messages --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <h5 class="text-secondary">
                                <i class="bi bi-card-checklist"></i> Profile Details
                            </h5>
                            <hr>

                            {{-- Update Name/Email Form --}}
                            <form action="{{ route('admin.updateProfile') }}" method="POST" class="mb-4">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        value="{{ Auth::user()->name }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        required
                                    >
                                    @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input
                                        type="email"
                                        name="email"
                                        id="email"
                                        value="{{ Auth::user()->email }}"
                                        class="form-control @error('email') is-invalid @enderror"
                                        required
                                    >
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <button type="submit" class="btn btn-primary">Update Profile</button>
                            </form>

                            {{-- Update Password Form --}}
                            <h5 class="text-secondary">
                                <i class="bi bi-lock-fill"></i> Update Password
                            </h5>
                            <hr>

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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
