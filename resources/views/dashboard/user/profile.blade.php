@extends('layouts.user')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    {{-- Card Header --}}
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-person-lines-fill"></i> My Profile</h4>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">
                        {{-- Personal Information --}}
                        <h5 class="mb-3"><strong>Personal Information</strong></h5>
                        <form action="{{ route('user.updateName') }}" method="POST" id="nameForm">
                            @csrf
                            @method('PUT')

                            {{-- Name --}}
                            <div class="d-flex align-items-center mb-3">
                                <label for="nameInput" class="form-label me-2"><strong>Name:</strong></label>
                                <input type="text" name="name" id="nameInput"
                                       value="{{ $user->name }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       style="max-width: 300px;" disabled>

                                {{-- Edit/Save Buttons --}}
                                <button id="editNameButton" type="button" class="btn btn-outline-secondary btn-sm ms-3">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button id="saveNameButton" type="submit" class="btn btn-primary btn-sm ms-2 d-none">
                                    Save
                                </button>
                            </div>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </form>

                        {{-- Static Information --}}
                        <div><strong>Email:</strong> {{ $user->email }}</div>
                        <div><strong>Remaining Invites:</strong> {{ $user->remaining_invites ?? 'N/A' }}</div>
                        <div><strong>Email Verified At:</strong> {{ $user->email_verified_at ?? 'Not Verified' }}</div>
                        <div><strong>Member Since:</strong> {{ $user->created_at->isoFormat('MMMM D, YYYY [at] h:mm A') }}</div>

                        <hr>

                        {{-- Change Password --}}
                        <h5 class="mb-3"><strong>Change Password</strong></h5>
                        <form action="{{ route('user.updatePassword') }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Current Password --}}
                            <div class="mb-3">
                                <label for="current_password" class="form-label"><strong>Current Password</strong></label>
                                <input type="password" name="current_password" id="current_password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       required>
                                @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- New Password --}}
                            <div class="mb-3">
                                <label for="password" class="form-label"><strong>New Password</strong></label>
                                <input type="password" name="password" id="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       required>
                                @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label"><strong>Confirm Password</strong></label>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="form-control @error('password_confirmation') is-invalid @enderror"
                                       required>
                                @error('password_confirmation')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Action Buttons --}}
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Inline Script to handle Edit/Save functionality --}}
    <script>
        const editNameButton = document.getElementById('editNameButton');
        const saveNameButton = document.getElementById('saveNameButton');
        const nameInput = document.getElementById('nameInput');

        editNameButton.addEventListener('click', () => {
            nameInput.disabled = false;
            editNameButton.classList.add('d-none');
            saveNameButton.classList.remove('d-none');
        });
    </script>
@endsection
