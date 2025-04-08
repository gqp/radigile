@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Admin: Notify Me Submissions</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Company</th>
                <th>Notify Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($submissions as $submission)
                <tr>
                    <td>{{ $submission->id }}</td>
                    <td>{{ $submission->name }}</td>
                    <td>{{ $submission->email }}</td>
                    <td>{{ $submission->company ?? 'N/A' }}</td>
                    <td>
                        <span class="badge {{ $submission->notify_status ? 'bg-success' : 'bg-danger' }}">
                            {{ $submission->notify_status ? 'Enabled' : 'Disabled' }}
                        </span>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.notify-me.toggle', $submission->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                Toggle Status
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
