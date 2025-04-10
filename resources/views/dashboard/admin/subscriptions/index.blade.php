@extends('layouts.app')

@section('content')
<div class="app-container-fluid">
    <div class="container">
        <h1>Manage Subscriptions</h1>
        <table class="table">
            <thead>
            <tr>
                <th>User</th>
                <th>Plan</th>
                <th>Starts At</th>
                <th>Ends At</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($subscriptions as $subscription)
                <tr>
                    <td>{{ $subscription->user->name }}</td>
                    <td>{{ $subscription->plan->name }}</td>
                    <td>{{ $subscription->starts_at->format('Y-m-d') }}</td>
                    <td>{{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : 'N/A' }}</td>
                    <td>{{ $subscription->is_active ? 'Active' : 'Inactive' }}</td>
                    <td>
                        <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" class="btn btn-sm btn-warning">
                            Edit
                        </a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
