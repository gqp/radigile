@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container mt-4">

    @foreach (['success', 'error', 'info'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-1">{{ $team->name }}</h2>
            <p class="text-muted mb-0">
                <span class="badge bg-secondary me-1">{{ $team->domain->name ?? '—' }}</span>
                <span class="badge bg-info text-dark">{{ $team->team_frameq->name ?? '—' }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('user.assessments.create', ['team_id' => $team->id]) }}" class="btn btn-success btn-sm">
                <i class="bi bi-clipboard-plus"></i> Create Assessment
            </a>
            <a href="{{ route('admin.teams.edit', $team->id) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.teams.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div class="row g-4">

        {{-- Team Details --}}
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-light"><strong><i class="bi bi-info-circle"></i> Team Details</strong></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">Owner</dt>
                        <dd class="col-sm-7">
                            {{ $team->owner->name ?? '—' }}
                            @if ($team->owner)
                                <br><small class="text-muted">{{ $team->owner->email }}</small>
                            @endif
                        </dd>

                        <dt class="col-sm-5">Domain</dt>
                        <dd class="col-sm-7">{{ $team->domain->name ?? '—' }}</dd>

                        <dt class="col-sm-5">Framework</dt>
                        <dd class="col-sm-7">{{ $team->team_frameq->name ?? '—' }}</dd>

                        <dt class="col-sm-5">Members</dt>
                        <dd class="col-sm-7">{{ $team->members->count() }}</dd>

                        <dt class="col-sm-5">Assessments</dt>
                        <dd class="col-sm-7">{{ $team->assessments->count() }}</dd>

                        <dt class="col-sm-5">Created</dt>
                        <dd class="col-sm-7">{{ $team->created_at->format('d M Y') }}</dd>

                        @if ($team->description)
                            <dt class="col-sm-5">Description</dt>
                            <dd class="col-sm-7">{{ $team->description }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-8">

            {{-- Members --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-people"></i> Members ({{ $team->members->count() }})</strong>
                    <a href="{{ route('admin.teams.members.create', $team->id) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus"></i> Add Member
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($team->members->isEmpty())
                        <p class="text-muted p-3 mb-0">No members yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Joined</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($team->members as $member)
                                    <tr>
                                        <td>{{ $member->name }}</td>
                                        <td class="text-muted small">{{ $member->email }}</td>
                                        <td><span class="badge bg-secondary">{{ ucfirst($member->pivot->role) }}</span></td>
                                        <td class="text-muted small">{{ $member->pivot->created_at?->format('d M Y') ?? '—' }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.teams.members.edit', [$team->id, $member->id]) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Assessments --}}
            <div class="card shadow-sm">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-clipboard-list"></i> Assessments ({{ $team->assessments->count() }})</strong>
                    <a href="{{ route('user.assessments.create', ['team_id' => $team->id]) }}" class="btn btn-success btn-sm">
                        <i class="bi bi-plus"></i> New Assessment
                    </a>
                </div>
                <div class="card-body p-0">
                    @if ($team->assessments->isEmpty())
                        <p class="text-muted p-3 mb-0">No assessments yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th class="text-center">Questions</th>
                                        <th class="text-center">Responses</th>
                                        <th>Created</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($team->assessments as $assessment)
                                    <tr>
                                        <td><strong>{{ $assessment->title }}</strong></td>
                                        <td>
                                            @if ($assessment->isDraft())
                                                <span class="badge bg-secondary">Draft</span>
                                            @elseif ($assessment->isActive())
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-dark">Closed</span>
                                            @endif
                                        </td>
                                        <td class="text-center">{{ $assessment->questions->count() }}</td>
                                        <td class="text-center">{{ $assessment->results->count() }}</td>
                                        <td class="text-muted small">{{ $assessment->created_at->format('d M Y') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('user.assessments.show', $assessment) }}" class="btn btn-sm btn-outline-primary">
                                                {{ $assessment->isClosed() ? 'Results' : 'Manage' }}
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
