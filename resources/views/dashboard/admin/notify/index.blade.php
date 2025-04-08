@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Admin: Notify Me Submissions</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Global Toggle for Notify Me --}}
        <div class="mb-4">
            <h5>
                Notify Me Status:
                <span class="badge {{ $notifyMeStatus ? 'bg-success' : 'bg-danger' }}">
                {{ $notifyMeStatus ? 'Enabled' : 'Disabled' }}
            </span>
            </h5>
            <form method="POST" action="{{ route('admin.notify-me.toggle-global') }}">
                @csrf
                <button type="submit" class="btn btn-primary">
                    {{ $notifyMeStatus ? 'Disable' : 'Enable' }} Notify Me
                </button>
            </form>
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
