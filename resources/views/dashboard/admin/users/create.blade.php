@extends('layouts.admin')

@section('content')
    <!-- Include Navbar -->
    @include('layouts.admin.navbar')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                {{-- Card for Creating User --}}
                <div class="card shadow-sm">
                    {{-- Card Header --}}
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-person-plus"></i> Create New User</h4>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Users
                        </a>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.store') }}">
                            @csrf

                            {{-- Name --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       placeholder="Enter user name" required>
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="Enter user email" required>
                            </div>

                            {{-- Role --}}
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select name="role" id="role" class="form-control" required>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Plan (Optional) --}}
                            <div class="mb-3">
                                <label for="subscription" class="form-label">Select Subscription Plan (Optional)</label>
                                <select name="subscription" id="subscription" class="form-control">
                                    <option value="">No Plan</option>
                                    @foreach($plans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }} - ${{ $plan->price }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Test User Options --}}
                            <div class="mb-3">
                                <h5>Test User Options</h5>

                                {{-- Check if it's a test user --}}
                                <div class="form-check">
                                    <input type="checkbox" name="test_user" id="test_user" class="form-check-input" value="1">
                                    <label for="test_user" class="form-check-label">
                                        Create as Test User
                                    </label>
                                </div>

                                {{-- Send NewUserNotification Checkbox --}}
                                <div class="form-check mt-3" id="send-notification-section" style="display: none;">
                                    <input type="checkbox" name="send_notification" id="send_notification" class="form-check-input" value="1">
                                    <label for="send_notification" class="form-check-label">
                                        Send New User Notification (Test Users Only)
                                    </label>
                                </div>

                                {{-- Conditionally Display Password Fields --}}
                                <div id="test-user-password-section" class="mt-3" style="display: none;">
                                    <label>Password (Optional for Test Users)</label>
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Enter password">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                           class="form-control mt-2" placeholder="Confirm password">
                                </div>

                                {{-- Skip Email Verification --}}
                                <div class="form-check mt-3" id="skip-verification-section" style="display: none;">
                                    <input type="checkbox" name="skip_verification" id="skip_verification" class="form-check-input" value="1">
                                    <label for="skip_verification" class="form-check-label">Skip Email Verification</label>
                                </div>
                            </div>

                            {{-- Submit Button --}}
                            <button type="submit" class="btn btn-primary">Create User</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const testUserCheckbox = document.getElementById('test_user');
            const sendNotificationCheckbox = document.getElementById('send_notification');
            const passwordSection = document.getElementById('test-user-password-section');
            const sendNotificationSection = document.getElementById('send-notification-section');
            const skipVerificationSection = document.getElementById('skip-verification-section');

            testUserCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    sendNotificationSection.style.display = 'block';
                    skipVerificationSection.style.display = 'block';
                    sendNotificationCheckbox.checked = false; // Reset Send Notification
                    passwordSection.style.display = 'block'; // Show password by default
                } else {
                    sendNotificationSection.style.display = 'none';
                    passwordSection.style.display = 'none';
                    skipVerificationSection.style.display = 'none';
                }
            });

            sendNotificationCheckbox.addEventListener('change', function () {
                if (this.checked) {
                    passwordSection.style.display = 'none';
                } else {
                    passwordSection.style.display = 'block';
                }
            });
        });
    </script>
@endsection
