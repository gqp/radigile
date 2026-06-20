@extends('layouts.user')

@section('content')
    <h1>Team: {{ $team->name }}</h1>

    <p><strong>Description:</strong> {{ $team->description ?? 'No description provided' }}</p>
    <p><strong>Privacy:</strong> {{ ucfirst($team->type) }}</p>
    <p><strong>Framework:</strong> {{ $team->framework ?? 'N/A' }}</p>
    <p><strong>Created By:</strong> {{ $team->owner->name }}</p>
    <p><strong>Created At:</strong> {{ $team->created_at->format('d M, Y') }}</p>

    @if (!empty($team->agile_practices))
        <p><strong>Agile Practices:</strong> {{ implode(', ', $team->agile_practices) }}</p>
    @else
        <p><em>No agile practices have been assigned to this team.</em></p>
    @endif

    <h2>Team Members</h2>
    @if ($team->members->count() > 0)
        <ul>
            @foreach ($team->members as $member)
                <li>{{ $member->user->name }} ({{ ucfirst($member->role) }})</li>
            @endforeach
        </ul>
    @else
        <p><em>This team currently has no members.</em></p>
    @endif
@endsection
