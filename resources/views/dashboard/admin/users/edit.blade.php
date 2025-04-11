@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                {{-- Card for Editing User --}}
                <div class="card shadow-sm">
                    {{-- Card Header --}}
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-pencil-fill"></i> Edit User</h4>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Users
                        </a>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                            @csrf
                            @method('PUT')

                            {{-- Name --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
                            </div>

                            {{-- Status (Active/Inactive) --}}
                            <div class="mb-3">
                                <h5>User Status</h5>
                                <label class="me-3">
                                    <input type="radio" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}> Active
                                </label>
                                <label>
                                    <input type="radio" name="is_active" value="0" {{ !$user->is_active ? 'checked' : '' }}> Inactive
                                </label>
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
                                            {{ $user->roles->contains('name', $role->name) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Subscription --}}
                            <div class="mb-3">
                                <label for="subscription" class="form-label">Subscription Plan</label>
                                <select name="subscription" id="subscription" class="form-control">
                                    <option value="">Select a plan</option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->id }}" {{ optional($user->subscription)->plan_id == $plan->id ? 'selected' : '' }}>
                                            {{ $plan->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Save Changes</button>
                                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle"></i> Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
