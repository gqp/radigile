@extends('layouts.app')

@section('content')
    <h1>Invite Management</h1>
    <a href="#" onclick="document.getElementById('toggle-form').submit();">
        Toggle Invite Only: {{ $inviteOnly ? 'ON' : 'OFF' }}
    </a>

    <form id="toggle-form" action="{{ route('admin.invites.toggle') }}" method="POST">
        @csrf
    </form>

    <form method="POST" action="{{ route('admin.invites.create') }}">
        @csrf
        <label>Max Uses:</label>
        <input type="number" name="max_uses" required>
        <button type="submit">Generate Invite</button>
    </form>

    <table>
        <tr><th>Code</th><th>Creator</th><th>Times Used</th><th>Max Uses</th></tr>
        @foreach($invites as $invite)
            <tr>
                <td>{{ $invite->code }}</td>
                <td>{{ $invite->creator->name }}</td>
                <td>{{ $invite->times_used }}/{{ $invite->max_uses }}</td>
            </tr>
        @endforeach
    </table>
@endsection
