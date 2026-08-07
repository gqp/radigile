@extends('layouts.app')

@section('content')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <h1>Edit Team: {{ $team->name }}</h1>

    <form method="POST" action="{{ route('admin.teams.update', $team->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Team Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $team->name) }}" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description">{{ old('description', $team->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="team_domain_id" class="form-label">Team Domain</label>
            <select class="form-control" id="team_domain_id" name="team_domain_id" required>
                @foreach ($teamDomains as $domain)
                    <option value="{{ $domain->id }}" {{ old('team_domain_id', $team->team_domain_id) == $domain->id ? 'selected' : '' }}>
                        {{ $domain->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="team_framework_id" class="form-label">Team Framework</label>
            <select class="form-control" id="team_framework_id" name="team_framework_id">
                <option value="" {{ old('team_framework_id', $team->team_framework_id) === null ? 'selected' : '' }}>None</option>
                @foreach ($teamFrameworks as $framework)
                    <option value="{{ $framework->id }}" {{ old('team_framework_id', $team->team_framework_id) == $framework->id ? 'selected' : '' }}>
                        {{ $framework->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="open_to_join_requests" name="open_to_join_requests" value="1"
                   {{ old('open_to_join_requests', $team->open_to_join_requests) ? 'checked' : '' }}>
            <label for="open_to_join_requests" class="form-check-label">Open to join requests</label>
        </div>

        <div class="mb-3">
            <label for="owner_id" class="form-label">Team Owner</label>
            <select class="form-control" id="owner_id" name="owner_id" required>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" {{ old('owner_id', $team->owner_id) == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="team_members" class="form-label">Team Members</label>
            <select class="form-control" id="team_members" name="team_members[]" multiple>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}"
                        {{ in_array($user->id, old('team_members', $team->members->pluck('id')->toArray())) ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Team</button>
    </form>

@endsection
