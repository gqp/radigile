@extends('layouts.admin')

@section('content')
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

                            {{-- Password --}}
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Enter password" required>
                            </div>

                            {{-- Confirm Password --}}
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                       name="password_confirmation" placeholder="Confirm password" required>
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
                                        >
                                        <label class="form-check-label"
                                               for="role-{{ $role->id }}">{{ $role->name }}</label>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Subscription --}}
                            <div class="mb-3">
                                <label for="subscription" class="form-label">Assign Subscription Plan (Optional)</label>
                                <select name="subscription" id="subscription" class="form-control">
                                    <option value="">Select a plan</option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Save
                                    User
                                </button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i
                                            class="bi bi-x-circle"></i> Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
