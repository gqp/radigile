@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h1>Profile: {{ $user->name }}</h1>
                    </div>

                    <div class="card-body">
                        <div>
                            <h4>Personal Information</h4>
                            <ul>
                                <li><strong>Name:</strong> {{ $user->name }}</li>
                                <li><strong>Email:</strong> {{ $user->email }}</li>
                                <li><strong>Remaining Invites:</strong> {{ $user->remaining_invites }}</li>
                                <li><strong>Email Verified At:</strong> {{ $user->email_verified_at ?? 'Not Verified' }}</li>
                                <li><strong>Created At:</strong> {{ $user->created_at->format('Y-m-d H:i:s') }}</li>
                                <li><strong>Updated At:</strong> {{ $user->updated_at->format('Y-m-d H:i:s') }}</li>
                            </ul>
                        </div>

                        <hr>

                        <div>
                            <h4>Change Password</h4>
                            <form action="{{ route('user.updatePassword') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input
                                        type="password"
                                        name="current_password"
                                        id="current_password"
                                        class="form-control"
                                        required
                                    >
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input
                                        type="password"
                                        name="new_password"
                                        id="new_password"
                                        class="form-control"
                                        required
                                    >
                                </div>

                                <div class="mb-3">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                    <input
                                        type="password"
                                        name="new_password_confirmation"
                                        id="new_password_confirmation"
                                        class="form-control"
                                        required
                                    >
                                </div>

                                <button type="submit" class="btn btn-primary">Update Password</button>
                            </form>
                        </div>

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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
