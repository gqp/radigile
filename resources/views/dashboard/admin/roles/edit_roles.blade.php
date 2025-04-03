@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Roles</h1>
        <a href="{{ route('roles.create') }}" class="btn btn-primary">Create Role</a>
        <table class="table mt-3">
            <thead>
            <tr>
                <th>Name</th>
                <th>Permissions</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->name }}</td>
                    <td>
                        {{ $role->permissions->pluck('name')->join(', ') }}
                    </td>
                    <td>
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning">Edit</a>
                        <form method="POST" action="{{ route('roles.destroy', $role->id) }}" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
