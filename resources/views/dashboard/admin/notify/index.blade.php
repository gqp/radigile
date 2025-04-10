@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Admin: Notify Me Submissions</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Global Toggle for Notify Me --}}
        <div class="mb-4">
            <h5>Notify Me Feature</h5>

            {{-- Notification System Status --}}
            <div class="alert alert-{{ \App\Models\Setting::get('notify_me') ? 'success' : 'danger' }}">
                <strong>Notify Me System is currently {{ \App\Models\Setting::get('notify_me') ? 'Enabled' : 'Disabled' }}.</strong>
                You can toggle it from the <a href="{{ route('admin.settings') }}">Settings Page</a>.
            </div>
        </div>

        {{-- Submissions Table --}}
        <h2>All Submissions</h2>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Company</th>
                <th>Submitted At</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($submissions as $submission)
                <tr>
                    <td>{{ $submission->id }}</td>
                    <td>{{ $submission->name }}</td>
                    <td>{{ $submission->email }}</td>
                    <td>{{ $submission->company ?? 'N/A' }}</td>
                    <td>{{ $submission->created_at }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center">No submissions found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
