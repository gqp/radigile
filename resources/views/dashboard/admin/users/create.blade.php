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

                            {{-- Test User Options --}}
                            <div class="mb-3">
                                <h5>Test User Options</h5>

                                {{-- Create as Test User --}}
                                <div class="form-check">
                                    <input type="checkbox" name="test_user" id="test_user" class="form-check-input" value="1">
                                    <label for="test_user" class="form-check-label">
                                        Create as Test User
                                    </label>
                                </div>

                                {{-- Skip Email Verification --}}
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="skip_verification" id="skip_verification"
                                           class="form-check-input" value="1">
                                    <label for="skip_verification" class="form-check-label">
                                        Skip Email Verification (for Test Users)
                                    </label>
                                </div>

                                {{-- Conditionally Show Password Fields for Test Users --}}
                                <div id="test-user-password-section" class="mt-3" style="display: none;">
                                    <label>Password (Optional for Test Users)</label>

                                    {{-- Password --}}
                                    <div class="mb-2">
                                        <input type="password" name="password" id="password" class="form-control"
                                               placeholder="Enter password">
                                    </div>

                                    {{-- Confirm Password --}}
                                    <div>
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                               class="form-control" placeholder="Confirm password">
                                    </div>
                                </div>
                            </div>

                            {{-- Roles --}}
                            <div class="mb-3">
                                <h5>Assign Roles</h5>
                                @foreach ($roles as $role)
                                    <div class="form-check">
                                        <input
                                            type="radio"
                                            class="form-check-input"
                                            name="role"
                                            id="role-{{ $role->id }}"
                                            value="{{ $role->name }}"
                                            required
                                        >
                                        <label class="form-check-label"
                                               for="role-{{ $role->id }}">{{ $role->name }}</label>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Subscription Plans --}}
                            <div class="mb-3">
                                <label for="subscription" class="form-label">Assign Subscription Plan (Optional)</label>
                                <select name="subscription" id="subscription" class="form-control">
                                    <option value="">No Plan</option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->price > 0 ? "$" . $plan->price : "Free" }})</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Submit Button --}}
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Create User
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- JavaScript to Toggle Password Fields for Test Users --}}
@section('script')
    <script>
        // Handle "Create as Test User" checkbox toggle
        const testUserCheckbox = document.getElementById('test_user');
        const testUserPasswordSection = document.getElementById('test-user-password-section');

        testUserCheckbox.addEventListener('change', (e) => {
            if (e.target.checked) {
                testUserPasswordSection.style.display = 'block';
            } else {
                testUserPasswordSection.style.display = 'none';
            }
        });
    </script>
@endsection
