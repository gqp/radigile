<span id="pending-assessments-total-value" class="d-none">{{ $pendingAssessments->total() }}</span>

@if ($pendingAssessments->isEmpty())
    <p class="text-muted p-3 mb-0">Nothing waiting for your response.</p>
@else
    {{-- Table view --}}
    <div class="table-responsive assessments-view-table">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <x-sortable-th field="title" label="Title" sort-param="pending_sort" dir-param="pending_dir" page-param="pending_page" />
                    <th>Team</th>
                    <th class="text-center">Maturity</th>
                    <th>Questions</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pendingAssessments as $assessment)
                <tr>
                    <td><strong>{{ $assessment->title }}</strong></td>
                    <td>{{ $assessment->team->name }}</td>
                    <td class="text-center">
                        @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 44])
                    </td>
                    <td>{{ $assessment->questions->count() }}</td>
                    <td class="text-end">
                        <a href="{{ route('user.assessments.take', $assessment) }}" class="btn btn-sm btn-success">Take Assessment</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Card view --}}
    <div class="row g-4 p-3 assessments-view-cards d-none">
        @foreach ($pendingAssessments as $assessment)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow border-0 assessment-card">
                <div class="card-header bg-warning text-dark d-flex align-items-center gap-2 py-3">
                    <i class="fas fa-hourglass-half fa-lg"></i>
                    <h5 class="mb-0 fw-bold text-truncate">{{ $assessment->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-3">
                        @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 110])
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2 mb-3 d-inline-block">
                        <i class="fas fa-users me-1"></i> {{ $assessment->team->name }}
                    </span>
                    <p class="text-muted small mb-0"><i class="fas fa-question-circle me-1"></i> {{ $assessment->questions->count() }} questions</p>
                </div>
                <div class="card-footer bg-white border-top-0 pb-3">
                    <a href="{{ route('user.assessments.take', $assessment) }}" class="btn btn-success w-100">Take Assessment</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="p-3">
        {{ $pendingAssessments->links() }}
    </div>
@endif
