@extends('layouts.app')

@section('content')
    <div class="container">
        <h1 class="mb-4">My Invites</h1>

        @if($invites->isEmpty())
            <p>No invites created yet.</p>
        @else
            <table class="table">
                <thead>
                <tr>
                    <th>Invite Code</th>
                    <th>Times Used</th>
                    <th>Max Uses</th>
                    <th>Expires At</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($invites as $invite)
                    <tr>
                        <td>{{ $invite->code }}</td>
                        <td>{{ $invite->times_used }}</td>
                        <td>{{ $invite->max_uses }}</td>
                        <td>{{ $invite->expires_at ? $invite->expires_at->format('Y-m-d H:i') : 'Never' }}</td>
                        <td>
                            <button class="btn btn-primary share-btn" data-id="{{ $invite->id }}" data-code="{{ $invite->code }}">
                                Share
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.share-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const inviteId = this.getAttribute('data-id');

                    // Example AJAX request simulation to share invite
                    fetch(`/user/invites/${inviteId}/share`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            alert(data.message + '\nShare Link: ' + data.share_link);
                        })
                        .catch(error => {
                            console.error('Error sharing invite:', error);
                        });
                });
            });
        });
    </script>
@endsection
