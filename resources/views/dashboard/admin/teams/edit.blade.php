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
            <label for="type" class="form-label">Team Visibility</label>
            <select class="form-control" id="type" name="type" required>
                <option value="private" {{ old('type', $team->type) === 'private' ? 'selected' : '' }}>Private</option>
                <option value="public" {{ old('type', $team->type) === 'public' ? 'selected' : '' }}>Public</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="framework" class="form-label">Team Framework</label>
            <select class="form-control" id="framework" name="framework">
                <option value="" {{ old('framework', $team->framework) === null ? 'selected' : '' }}>None</option>
                <option value="Scrum" {{ old('framework', $team->framework) === 'Scrum' ? 'selected' : '' }}>Scrum</option>
                <option value="Agile" {{ old('framework', $team->framework) === 'Agile' ? 'selected' : '' }}>Agile</option>
                <option value="Kanban" {{ old('framework', $team->framework) === 'Kanban' ? 'selected' : '' }}>Kanban</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="team_domain" class="form-label">Team Domain</label>
            <select class="form-control" id="team_domain" name="team_domain" required>
                <option value="devops" {{ old('team_domain', $team->team_domain) === 'devops' ? 'selected' : '' }}>DevOps</option>
                <option value="product" {{ old('team_domain', $team->team_domain) === 'product' ? 'selected' : '' }}>Product</option>
                <option value="development" {{ old('team_domain', $team->team_domain) === 'development' ? 'selected' : '' }}>Development</option>
                <option value="design" {{ old('team_domain', $team->team_domain) === 'design' ? 'selected' : '' }}>Design</option>
                <option value="marketing" {{ old('team_domain', $team->team_domain) === 'marketing' ? 'selected' : '' }}>Marketing</option>
            </select>
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
