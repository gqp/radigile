@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0"><i class="fas fa-pencil-alt"></i> Edit Team: <strong>{{ $team->name }}</strong></h4>
                <a href="{{ route('user.teams.show', $team->id) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-light"><strong>Team Details</strong></div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.teams.update', $team->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Team Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', $team->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description', $team->description) }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label fw-semibold">Visibility</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="private" {{ old('type', $team->type) === 'private' ? 'selected' : '' }}>Private</option>
                                <option value="public"  {{ old('type', $team->type) === 'public'  ? 'selected' : '' }}>Public</option>
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="team_framework_id" class="form-label fw-semibold">Framework <span class="text-danger">*</span></label>
                            <select name="team_framework_id" id="team_framework_id"
                                    class="form-select @error('team_framework_id') is-invalid @enderror" required>
                                <option value="" disabled>Select a Framework</option>
                                @foreach ($teamFrameworks as $framework)
                                    <option value="{{ $framework->id }}"
                                            {{ old('team_framework_id', $team->team_framework_id) == $framework->id ? 'selected' : '' }}>
                                        {{ $framework->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('team_framework_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-4">
                            <label for="team_domain_id" class="form-label fw-semibold">Domain <span class="text-danger">*</span></label>
                            <select name="team_domain_id" id="team_domain_id"
                                    class="form-select @error('team_domain_id') is-invalid @enderror" required>
                                <option value="" disabled>Select a Domain</option>
                                @foreach ($teamDomains as $domain)
                                    <option value="{{ $domain->id }}"
                                            {{ old('team_domain_id', $team->team_domain_id) == $domain->id ? 'selected' : '' }}>
                                        {{ $domain->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('team_domain_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                            <a href="{{ route('user.teams.show', $team->id) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
