<span id="completed-assessments-total-value" class="d-none">{{ $completedAssessments->total() }}</span>

@if ($completedAssessments->isEmpty())
    <p class="text-muted p-3 mb-0">No completed assessments waiting on the owner to close.</p>
@else
    {{-- Table view --}}
    <div class="table-responsive assessments-view-table">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <x-sortable-th field="title" label="Title" sort-param="completed_sort" dir-param="completed_dir" page-param="completed_page" />
                    <th>Team</th>
                    <th class="text-center">Maturity</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($completedAssessments as $assessment)
                <tr>
                    <td><strong>{{ $assessment->title }}</strong></td>
                    <td>{{ $assessment->team->name }}</td>
                    <td class="text-center">
                        @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 44])
                    </td>
                    <td><span class="badge bg-info text-dark">Submitted — waiting for owner to close</span></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Card view --}}
    <div class="row g-4 p-3 assessments-view-cards d-none">
        @foreach ($completedAssessments as $assessment)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow border-0 assessment-card">
                <div class="card-header bg-info text-dark d-flex align-items-center gap-2 py-3">
                    <i class="fas fa-check-circle fa-lg"></i>
                    <h5 class="mb-0 fw-bold text-truncate">{{ $assessment->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-3">
                        @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 110])
                    </div>
                    <span class="badge bg-light text-dark border px-3 py-2 mb-3 d-inline-block">
                        <i class="fas fa-users me-1"></i> {{ $assessment->team->name }}
                    </span>
                    <p class="text-muted small mb-0">Submitted — waiting for owner to close</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="p-3">
        {{ $completedAssessments->links() }}
    </div>
@endif
