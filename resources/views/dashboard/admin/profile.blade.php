@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h1>
                            Hi Admin:
                            <span id="adminName">{{ Auth::user()->name }}</span>
                        </h1>
                    </div>

                    <div class="card-body">
                        {{-- Admin Profile Section --}}
                        <span><strong>Profile</strong></span>

                        <hr>

                        {{-- Name Edit Section --}}
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
                        {{-- Prettier Joined Date --}}
                        <div>
                            <strong>Member Since:</strong>
                            {{ Auth::user()->created_at->isoFormat('MMMM D, YYYY [at] h:mm A') }}
                        </div>

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
            const editAdminNameButton = document.getElementById('editAdminNameButton');
            const saveAdminNameButton = document.getElementById('saveAdminNameButton');
            const adminNameInput = document.getElementById('adminNameInput');

            editAdminNameButton.addEventListener('click', function () {
                // Toggle the name field's disabled attribute
                adminNameInput.disabled = !adminNameInput.disabled;

                if (!adminNameInput.disabled) {
                    // Show Save button and focus input field
                    saveAdminNameButton.classList.remove('d-none');
                    adminNameInput.focus();
                } else {
                    // Hide Save button if the input is disabled
                    saveAdminNameButton.classList.add('d-none');
                }
            });
        });
    </script>
@endsection
