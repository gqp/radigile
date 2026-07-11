@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container mt-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">All Assessments</h2>
        <a href="{{ route('user.assessments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Create Assessment
        </a>
    </div>

    @if ($assessments->isEmpty())
        <div class="alert alert-light border">No assessments have been created yet.</div>
    @else
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Team</th>
                                <th>Created By</th>
                                <th>Status</th>
                                <th class="text-center">Questions</th>
                                <th class="text-center">Responses</th>
                                <th class="text-center">Evaluators</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($assessments as $assessment)
                            <tr>
                                <td><strong>{{ $assessment->title }}</strong>
                                    @if ($assessment->description)
                                        <br><small class="text-muted">{{ Str::limit($assessment->description, 60) }}</small>
                                    @endif
                                </td>
                                <td>{{ $assessment->team->name }}</td>
                                <td>{{ $assessment->creator->name }}</td>
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
                                <td class="text-center">{{ $assessment->responses->pluck('user_id')->unique()->count() }}</td>
                                <td class="text-center">{{ $assessment->evaluators->count() }}</td>
                                <td>{{ $assessment->created_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
