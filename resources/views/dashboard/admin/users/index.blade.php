@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>User Management</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">Add New User</a>
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Subscription</th> {{-- Add subscription column --}}
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        {{-- Display the role(s) of the user --}}
                        @if($user->roles->isNotEmpty())
                            {{ $user->roles->pluck('name')->join(', ') }}
                        @else
                            <span class="text-muted">No role assigned</span>
                        @endif
                    </td>
                    <td>
                        {{-- Display the subscription plan --}}
                        @if($user->subscription)
                            {{ $user->subscription->plan->name }} {{-- Show plan name --}}
                        @else
                            <span class="text-muted">No subscription</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
