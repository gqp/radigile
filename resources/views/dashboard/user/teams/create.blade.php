@extends('layouts.admin')

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

    <h1>Create a New Team</h1>

    <form method="POST" action="{{ route('admin.teams.store') }}">
        @csrf

        <!-- Team Name -->
        <div class="mb-3">
            <label for="name" class="form-label">Team Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-3">
            <label for="description" class="form-label">Description (Optional)</label>
            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description">{{ old('description') }}</textarea>
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Visibility -->
        <div class="mb-3">
            <label for="type" class="form-label">Team Visibility</label>
            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                <option value="private" {{ old('type') === 'private' ? 'selected' : '' }}>Private</option>
                <option value="public" {{ old('type') === 'public' ? 'selected' : '' }}>Public</option>
            </select>
            @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Team Domain -->
        <div class="mb-3">
            <label for="team_domain_id" class="form-label">Team Domain</label>
            <select name="team_domain_id" id="team_domain_id" class="form-select @error('team_domain_id') is-invalid @enderror" required>
                <option value="" disabled selected>Select a domain</option>
                @foreach($teamDomains as $domain)
                    <option value="{{ $domain->id }}" {{ old('team_domain_id') == $domain->id ? 'selected' : '' }}>{{ $domain->name }}</option>
                @endforeach
            </select>
            @error('team_domain_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Team Framework -->
        <div class="mb-3">
            <label for="team_framework" class="form-label">Team Framework (Optional)</label>
            <select name="team_framework" id="team_framework" class="form-select @error('team_framework') is-invalid @enderror">
                <option value="" selected>No framework</option>
                @foreach($teamFrameworks as $framework)
                    <option value="{{ $framework->id }}" {{ old('team_framework') == $framework->id ? 'selected' : '' }}>{{ $framework->name }}</option>
                @endforeach
            </select>
            @error('team_framework')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Team Owner -->
        <div class="mb-3">
            <label for="owner_id" class="form-label">Team Owner</label>
            <select name="owner_id" id="owner_id" class="form-control @error('owner_id') is-invalid @enderror" required>
                <option value="" disabled selected>Select a team owner</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('owner_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>
            @error('owner_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Team Members -->
        <div class="mb-3">
            <label for="team_members" class="form-label">Add Team Members</label>
            <select name="team_members[]" id="team_members" class="form-control @error('team_members') is-invalid @enderror" multiple>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ collect(old('team_members'))->contains($user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                @endforeach
            </select>
            @error('team_members')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Create Team</button>
    </form>

@endsection
