<span id="my-assessments-total-value" class="d-none">{{ $myAssessments->total() }}</span>

@if ($myAssessments->isEmpty())
    <p class="text-muted p-3 mb-0">You haven't created any assessments yet.</p>
@else
    {{-- Table view --}}
    <div class="table-responsive assessments-view-table">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <x-sortable-th field="title" label="Title" sort-param="my_sort" dir-param="my_dir" page-param="my_page" />
                    <th>Team</th>
                    <th class="text-center">Maturity</th>
                    <x-sortable-th field="status" label="Status" sort-param="my_sort" dir-param="my_dir" page-param="my_page" />
                    <th>Questions</th>
                    <th>Responses</th>
                    <x-sortable-th field="created_at" label="Created" sort-param="my_sort" dir-param="my_dir" page-param="my_page" />
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($myAssessments as $assessment)
                <tr>
                    <td><strong>{{ $assessment->title }}</strong></td>
                    <td>{{ $assessment->team->name }}</td>
                    <td class="text-center">
                        @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 44])
                    </td>
                    <td>
                        @if ($assessment->isDraft())
                            <span class="badge bg-secondary">Draft</span>
                        @elseif ($assessment->isActive())
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-dark">Closed</span>
                        @endif
                    </td>
                    <td>{{ $assessment->questions->count() }}</td>
                    <td>{{ $assessment->responses->pluck('user_id')->unique()->count() }}</td>
                    <td>{{ $assessment->created_at->format('d M Y') }}</td>
                    <td class="text-end">
                        <a href="{{ route('user.assessments.show', $assessment) }}" class="btn btn-sm btn-outline-primary">Manage</a>
                        @if ($assessment->isClosed())
                            <a href="{{ route('user.assessments.results', $assessment) }}" class="btn btn-sm btn-outline-dark">Results</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Card view --}}
    <div class="row g-4 p-3 assessments-view-cards d-none">
        @foreach ($myAssessments as $assessment)
        @php
            $headerClass = $assessment->isDraft() ? 'bg-secondary' : ($assessment->isActive() ? 'bg-success' : 'bg-dark');
        @endphp
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow border-0 assessment-card">
                <div class="card-header {{ $headerClass }} text-white d-flex align-items-center gap-2 py-3">
                    <i class="fas fa-clipboard-list fa-lg"></i>
                    <h5 class="mb-0 fw-bold text-truncate">{{ $assessment->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-3">
                        @include('dashboard.partials.mini-radar', ['team' => $assessment->team, 'size' => 110])
                    </div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-users me-1"></i> {{ $assessment->team->name }}
                        </span>
                        <span class="badge {{ $headerClass }} px-3 py-2">
                            @if ($assessment->isDraft()) Draft @elseif ($assessment->isActive()) Active @else Closed @endif
                        </span>
                    </div>
                    <p class="mb-1 small text-muted"><i class="fas fa-question-circle me-1"></i> {{ $assessment->questions->count() }} questions</p>
                    <p class="mb-1 small text-muted"><i class="fas fa-reply me-1"></i> {{ $assessment->responses->pluck('user_id')->unique()->count() }} responses</p>
                    <p class="text-muted small mb-0"><i class="fas fa-calendar me-1"></i> Created {{ $assessment->created_at->format('d M Y') }}</p>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex gap-2 pb-3">
                    <a href="{{ route('user.assessments.show', $assessment) }}" class="btn btn-primary flex-fill">Manage</a>
                    @if ($assessment->isClosed())
                        <a href="{{ route('user.assessments.results', $assessment) }}" class="btn btn-outline-dark flex-fill">Results</a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="p-3">
        {{ $myAssessments->links() }}
    </div>
@endif
