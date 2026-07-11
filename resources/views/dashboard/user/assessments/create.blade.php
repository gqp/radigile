@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-clipboard-plus"></i> Create Assessment</h4>
                    <a href="{{ route('user.assessments.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($teams->isEmpty())
                        <div class="alert alert-warning">
                            You need to <a href="{{ route('user.teams.create') }}">create a team</a> before you can create an assessment.
                        </div>
                    @else
                        <form method="POST" action="{{ route('user.assessments.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="team_id" class="form-label">Team</label>
                                <select name="team_id" id="team_id" class="form-select @error('team_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select a team</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}" {{ (old('team_id') ?? request('team_id')) == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="title" class="form-label">Assessment Title</label>
                                <input type="text" name="title" id="title"
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title') }}" placeholder="e.g. Q3 Team Maturity Check" required>
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">Description <small class="text-muted">(optional)</small></label>
                                <textarea name="description" id="description" rows="3"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="What is this assessment for?">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Create &amp; Add Questions <i class="bi bi-arrow-right"></i>
                            </button>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
