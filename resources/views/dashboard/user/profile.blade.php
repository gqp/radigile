@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h1>Profile</h1>
                    </div>

                    <div class="card-body">
                        {{-- Personal Information --}}
                        <div>
                            <h4>Personal Information</h4>
                            <form action="{{ route('user.updateName') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="name" class="form-label"><strong>Name:</strong></label>
                                    <input
                                        type="text"
                                        name="name"
                                        id="name"
                                        value="{{ old('name', $user->name) }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        required
                                    >
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div>
                                    <strong>Email:</strong> {{ $user->email }}
                                </div>

                                <div>
                                    <strong>Remaining Invites:</strong> {{ $user->remaining_invites }}
                                </div>

                                <div>
                                    <strong>Email Verified At:</strong> {{ $user->email_verified_at ?? 'Not Verified' }}
                                </div>

                                <div>
                                    <strong>Created At:</strong> {{ $user->created_at->format('Y-m-d H:i:s') }}
                                </div>

                                <div class="mt-3">
                                    <button class="btn btn-primary" type="submit">Update Name</button>
                                </div>
                            </form>
                        </div>

                        <hr>

                        {{-- Change Password --}}
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
