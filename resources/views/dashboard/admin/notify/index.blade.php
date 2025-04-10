@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <h1>Admin: Notify Me Submissions</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

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
                <th>Actions</th>
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
                    <td>
                        {{-- Send Invite Button --}}
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inviteModal-{{ $submission->id }}">
                            Send Invite
                        </button>

                        {{-- Invite Modal --}}
                        <div class="modal fade" id="inviteModal-{{ $submission->id }}" tabindex="-1" aria-labelledby="inviteModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.notify.send-invite', $submission->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="inviteModalLabel">Send Invite</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="form-group mb-3">
                                                <label for="max_uses">Maximum Uses:</label>
                                                <input type="number" name="max_uses" id="max_uses" class="form-control" min="1" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="expires_at">Expiration Date:</label>
                                                <input type="date" name="expires_at" id="expires_at" class="form-control">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Send</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">No submissions found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
