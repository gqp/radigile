@extends('layouts.app')

@section('content')
<div class="app-container">
    <div class="container">
        <h1>Manage Plans</h1>
        <a href="{{ route('admin.plans.create') }}" class="btn btn-primary mb-3">Create New Plan</a>
        <table class="table">
            <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Interval</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($plans as $plan)
                <tr>
                    <td>{{ $plan->name }}</td>
                    <td>{{ $plan->description }}</td>
                    <td>${{ number_format($plan->price, 2) }}</td>
                    <td>{{ ucfirst($plan->interval) }}</td>
                    <td>{{ $plan->is_active ? 'Active' : 'Inactive' }}</td>
                    <td><a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-warning">Edit</a></td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
