@extends('layouts.app')

@section('content')
    <h1>Admin: Manage Teams</h1>

    <a href="{{ route('admin.teams.create') }}" class="btn btn-primary">Create New Team</a>

    <table class="table mt-4">
        <thead>
        <tr>
            <th>Name</th>
            <th>Description</th>
            <th>Privacy</th>
            <th>Framework</th>
            <th>Domain</th> <!-- New Team Domain Column -->
            <th>Owner</th>
            <th>Members</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($teams as $team)
            <tr>
                <td>{{ $team->name }}</td>
                <td>{{ $team->description }}</td>
                <td>{{ ucfirst($team->type) }}</td>
                <td>{{ $team->framework ?? 'N/A' }}</td>
                <td>{{ ucfirst($team->team_domain) }}</td> <!-- New Team Domain Display -->
                <td>{{ $team->owner->name }}</td>
                <td>
                    @if ($team->members->count() > 0)
                        <ul>
                            @foreach ($team->members as $member)
                                <li>{{ $member->name }} ({{ $member->pivot->role }})</li>
                            @endforeach
                        </ul>
                    @else
                        No Members
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.teams.show', $team->id) }}" class="btn btn-sm btn-secondary">View</a>
                    <a href="{{ route('admin.teams.edit', $team->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.teams.destroy', $team->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDelete()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{-- Confirmation Script --}}
    <script>
        function confirmDelete() {
            return confirm('Are you sure you want to delete this team? This action cannot be undone.');
        }
    </script>
@endsection
