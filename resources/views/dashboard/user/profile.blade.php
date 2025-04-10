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
                            {{-- Name Field with Edit Button --}}
                            <form
                                action="{{ route('user.updateName') }}"
                                method="POST"
                                id="nameForm"
                            >
                                @csrf
                                @method('PUT')

                                <div class="d-flex align-items-center mb-3">
                                    <label for="nameInput" class="form-label me-2"><strong>Name:</strong></label>
                                    <input
                                        type="text"
                                        name="name"
                                        id="nameInput"
                                        value="{{ $user->name }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        style="max-width: 300px;"
                                        disabled
                                    >
                                    {{-- Edit Button --}}
                                    <button
                                        id="editNameButton"
                                        class="btn btn-sm btn-outline-secondary ms-3"
                                        type="button"
                                    >
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    {{-- Save Button (hidden initially) --}}
                                    <button
                                        id="saveNameButton"
                                        class="btn btn-sm btn-primary ms-2 d-none"
                                        type="submit"
                                    >
                                        Save
                                    </button>
                                </div>

                                @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </form>

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

    {{-- JavaScript for Toggle Behavior --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const editNameButton = document.getElementById('editNameButton');
            const saveNameButton = document.getElementById('saveNameButton');
            const nameInput = document.getElementById('nameInput');

            editNameButton.addEventListener('click', function () {
                // Toggle the name field's disabled attribute
                nameInput.disabled = !nameInput.disabled;

                if (!nameInput.disabled) {
                    // If the input is enabled, show the Save button
                    saveNameButton.classList.remove('d-none');
                    nameInput.focus(); // Focus on the input field for quick editing
                } else {
                    // If the input is disabled, hide the Save button
                    saveNameButton.classList.add('d-none');
                }
            });
        });
    </script>
@endsection
