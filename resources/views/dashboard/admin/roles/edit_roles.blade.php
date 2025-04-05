@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Role: {{ $role->name }}</h1>

        <form method="POST" action="{{ route('roles.update', $role->id) }}">
            @csrf
            @method('PUT')

            <!-- Role Name -->
            <div class="form-group">
                <label for="name">Role Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $role->name }}" required>
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
                               value="{{ $permission->id }}"
                            {{ $role->permissions->contains($permission) ? 'checked' : '' }}>
                        <label class="form-check-label" for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                    </div>
                @endforeach
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary mt-4">Update Role</button>
        </form>
    </div>
@endsection
