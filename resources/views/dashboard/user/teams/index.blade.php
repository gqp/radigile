@extends('layouts.user')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1>My Teams</h1>
        <a href="{{ route('user.teams.create') }}" class="btn btn-primary">Create Team</a>
    </div>

    {{-- Pending Invitations --}}
    @if($pendingInvites->count() > 0)
        <h2>Pending Invitations</h2>
        <ul>
            @foreach($pendingInvites as $invite)
                <li>
                    You are invited to join: <strong>{{ $invite->team->name }}</strong>
                    <form action="{{ route('user.teams.invite.accept', $invite->code) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">Accept</button>
                    </form>
                    <form action="{{ route('user.teams.invite.decline', $invite->code) }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">Decline</button>
                    </form>
                </li>
            @endforeach
        </ul>
    @endif

    {{-- Teams the user owns --}}
    <h2>Teams I Own</h2>
    @if ($ownedTeams->count() > 0)
        <table class="table mt-4">
            <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Privacy</th>
                <th>Framework</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($ownedTeams as $team)
                <tr>
                    <td>{{ $team->name }}</td>
                    <td>{{ $team->description ?? 'No description provided' }}</td>
                    <td>{{ ucfirst($team->type) }}</td>
                    <td>{{ $team->framework ?? 'N/A' }}</td>
                    <td>{{ $team->created_at->format('d M, Y') }}</td>
                    <td>
                        <a href="{{ route('user.teams.show', $team->id) }}" class="btn btn-sm btn-secondary">View</a>
                        <a href="{{ route('user.teams.edit', $team->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>You don't own any teams. Start by creating one!</p>
    @endif

    {{-- Teams the user is a member of --}}
    <h2>Teams I'm a Member Of</h2>
    @if ($memberTeams->count() > 0)
        <table class="table mt-4">
            <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Privacy</th>
                <th>Framework</th>
                <th>Created At</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($memberTeams as $team)
                <tr>
                    <td>{{ $team->name }}</td>
                    <td>{{ $team->description ?? 'No description provided' }}</td>
                    <td>{{ ucfirst($team->type) }}</td>
                    <td>{{ $team->framework ?? 'N/A' }}</td>
                    <td>{{ $team->created_at->format('d M, Y') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @else
        <p>You are not a member of any team yet.</p>
    @endif
@endsection
