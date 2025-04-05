@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Create New Role</h1>

        <!-- Form to create a new role -->
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf

            <!-- Role Name -->
            <div class="form-group">
                <label for="name">Role Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter role name" required>
            </div>

            <!-- Permissions -->
            <div class="form-group mt-3">
                <h4>Assign Permissions</h4>
                @foreach ($permissions as $permission)
                    <div class="form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               name="permissions[]"
                               id="permission-{{ $permission->id }}"
                               value="{{ $permission->id }}">
                        <label class="form-check-label" for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                    </div>
                @endforeach
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary mt-4">Create Role</button>
        </form>
    </div>
@endsection
