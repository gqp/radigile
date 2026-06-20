@extends('layouts.user')

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

    <form method="POST" action="{{ route('user.teams.update', $team->id) }}">
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
            <label for="type" class="form-label">Team Visibility</label>
            <select class="form-control" id="type" name="type" required>
                <option value="private" {{ old('type', $team->type) === 'private' ? 'selected' : '' }}>Private</option>
                <option value="public" {{ old('type', $team->type) === 'public' ? 'selected' : '' }}>Public</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="team_framework_id" class="form-label">Team Framework</label>
            <select name="team_framework_id" id="team_framework_id" class="form-control @error('team_framework_id') is-invalid @enderror" required>
                <option value="" disabled>Select a Framework</option>
                @foreach ($teamFrameworks as $framework)
                    <option value="{{ $framework->id }}" {{ old('team_framework_id', $team->team_framework_id) == $framework->id ? 'selected' : '' }}>
                        {{ $framework->name }}
                    </option>
                @endforeach
            </select>
            @error('team_framework_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="team_domain_id" class="form-label">Team Domain</label>
            <select name="team_domain_id" id="team_domain_id" class="form-control @error('team_domain_id') is-invalid @enderror" required>
                <option value="" disabled>Select a Domain</option>
                @foreach ($teamDomains as $domain)
                    <option value="{{ $domain->id }}" {{ old('team_domain_id', $team->team_domain_id) == $domain->id ? 'selected' : '' }}>
                        {{ $domain->name }}
                    </option>
                @endforeach
            </select>
            @error('team_domain_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Team Members (Read-only List) -->
        <div class="mb-3">
            <label for="team_members" class="form-label">Current Team Members</label>
            <ul class="list-group">
                @foreach ($members as $member)
                    <li class="list-group-item">
                        <strong>{{ $member->name }}</strong> ({{ $member->email }})
                    </li>
                @endforeach
            </ul>
            <small class="form-text text-muted">These are the current members of this team.</small>
        </div>

        <!-- Invitation Emails -->
        <div class="mb-3">
            <label for="invite_emails" class="form-label">Invite Members (Emails)</label>
            <textarea class="form-control @error('invite_emails') is-invalid @enderror"
                      id="invite_emails"
                      name="invite_emails"
                      rows="3"
                      placeholder="Enter email addresses separated by commas">{{ old('invite_emails') }}</textarea>
            <small class="form-text text-muted">Invite members by entering their email addresses separated by commas. Example: email1@example.com, email2@example.com</small>
            @error('invite_emails')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update Team</button>
    </form>

@endsection
