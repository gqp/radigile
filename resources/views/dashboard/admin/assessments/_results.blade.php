<span id="admin-assessments-total-value" class="d-none">{{ $assessments->total() }}</span>

@if ($assessments->isEmpty())
    <div class="alert alert-light border">No assessments found.</div>
@else
    <div class="card shadow-sm">
        <div class="card-body p-0">
            {{-- Table view --}}
            <div class="table-responsive admin-assessments-view-table">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <x-sortable-th field="title" label="Title" />
                            <th>Team</th>
                            <th>Created By</th>
                            <x-sortable-th field="status" label="Status" />
                            <th class="text-center">Questions</th>
                            <th class="text-center">Responses</th>
                            <th class="text-center">Evaluators</th>
                            <x-sortable-th field="created_at" label="Created" />
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
                            <td class="text-muted small">{{ $assessment->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Card view --}}
            <div class="row g-4 p-3 admin-assessments-view-cards d-none">
                @foreach ($assessments as $assessment)
                @php
                    $headerClass = $assessment->isDraft() ? 'bg-secondary' : ($assessment->isActive() ? 'bg-success' : 'bg-dark');
                @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 shadow border-0 admin-assessment-card">
                        <div class="card-header {{ $headerClass }} text-white d-flex align-items-center gap-2 py-3">
                            <i class="fas fa-clipboard-list fa-lg"></i>
                            <h5 class="mb-0 fw-bold text-truncate">{{ $assessment->title }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <span class="badge bg-light text-dark border px-3 py-2">
                                    <i class="fas fa-users me-1"></i> {{ $assessment->team->name }}
                                </span>
                                <span class="badge {{ $headerClass }} px-3 py-2">
                                    @if ($assessment->isDraft()) Draft @elseif ($assessment->isActive()) Active @else Closed @endif
                                </span>
                            </div>
                            <p class="mb-1 small text-muted"><i class="fas fa-user-edit me-1"></i> Created by {{ $assessment->creator->name }}</p>
                            <p class="mb-1 small text-muted"><i class="fas fa-question-circle me-1"></i> {{ $assessment->questions->count() }} questions</p>
                            <p class="mb-1 small text-muted"><i class="fas fa-reply me-1"></i> {{ $assessment->responses->pluck('user_id')->unique()->count() }} responses</p>
                            <p class="text-muted small mb-0"><i class="fas fa-calendar me-1"></i> Created {{ $assessment->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="p-3">
                {{ $assessments->links() }}
            </div>
        </div>
    </div>
@endif
