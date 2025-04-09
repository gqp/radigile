@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create New User</h1>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="subscription" class="form-label">Subscription Level</label>
                <select name="subscription" id="subscription" class="form-control">
                    <option value="">No Subscription</option>
                    @foreach ($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="d-flex justify-content-between mt-3">
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Save Changes</button>

                <!-- Cancel Button -->
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>
@endsection
