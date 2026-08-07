<span id="owned-teams-total-value" class="d-none">{{ $ownedTeams->total() }}</span>

@if ($ownedTeams->isEmpty())
    <p class="text-muted p-3 mb-0">You don't own any teams yet. <a href="{{ route('user.teams.create') }}">Create one</a>.</p>
@else
    {{-- Table view --}}
    <div class="table-responsive teams-view-table">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <x-sortable-th field="name" label="Name" sort-param="owned_sort" dir-param="owned_dir" page-param="owned_page" />
                    <th class="text-center">Maturity</th>
                    <th>Framework</th>
                    <th class="text-center">Members</th>
                    <th class="text-center">Pending</th>
                    <th class="text-center">Requests</th>
                    <x-sortable-th field="created_at" label="Created" sort-param="owned_sort" dir-param="owned_dir" page-param="owned_page" />
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ownedTeams as $team)
                <tr>
                    <td>
                        <strong>{{ $team->name }}</strong>
                        @if($team->description)
                            <br><small class="text-muted">{{ Str::limit($team->description, 60) }}</small>
                        @endif
                    </td>
                    <td class="text-center">
                        @include('dashboard.partials.mini-radar', ['team' => $team, 'size' => 44])
                    </td>
                    <td class="text-muted small">{{ $team->team_frameq->name ?? '—' }}</td>
                    <td class="text-center">{{ $team->members->count() }}</td>
                    <td class="text-center">
                        @if($team->pending_count > 0)
                            <a href="{{ route('user.teams.show', $team->id) }}"
                               class="badge bg-warning text-dark text-decoration-none"
                               title="{{ $team->pending_count }} invitation(s) awaiting response">
                                {{ $team->pending_count }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($team->pending_requests_count > 0)
                            <a href="{{ route('user.teams.show', $team->id) }}"
                               class="badge bg-danger text-decoration-none"
                               title="{{ $team->pending_requests_count }} join request(s) awaiting your approval">
                                {{ $team->pending_requests_count }}
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $team->created_at->format('d M Y') }}</td>
                    <td class="text-end">
                        <a href="{{ route('user.teams.show', $team->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                        <a href="{{ route('user.teams.edit', $team->id) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Card view --}}
    <div class="row g-4 p-3 teams-view-cards d-none">
        @foreach ($ownedTeams as $team)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow border-0 team-card">
                <div class="card-header bg-primary text-white d-flex align-items-center gap-2 py-3">
                    <i class="fas fa-users fa-lg"></i>
                    <h5 class="mb-0 fw-bold text-truncate">{{ $team->name }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-center mb-3">
                        @include('dashboard.partials.mini-radar', ['team' => $team, 'size' => 110])
                    </div>
                    @if($team->description)
                        <p class="text-muted mb-3">{{ Str::limit($team->description, 80) }}</p>
                    @endif
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-diagram-project me-1"></i> {{ $team->team_frameq->name ?? '—' }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-user-friends me-1"></i> {{ $team->members->count() }} member{{ $team->members->count() === 1 ? '' : 's' }}
                        </span>
                        @if($team->pending_count > 0)
                            <span class="badge bg-warning text-dark px-3 py-2">
                                <i class="fas fa-envelope me-1"></i> {{ $team->pending_count }} pending
                            </span>
                        @endif
                        @if($team->pending_requests_count > 0)
                            <span class="badge bg-danger px-3 py-2">
                                <i class="fas fa-user-clock me-1"></i> {{ $team->pending_requests_count }} join request{{ $team->pending_requests_count === 1 ? '' : 's' }}
                            </span>
                        @endif
                    </div>
                    <p class="text-muted small mb-0"><i class="fas fa-calendar me-1"></i> Created {{ $team->created_at->format('d M Y') }}</p>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex gap-2 pb-3">
                    <a href="{{ route('user.teams.show', $team->id) }}" class="btn btn-primary flex-fill">View</a>
                    <a href="{{ route('user.teams.edit', $team->id) }}" class="btn btn-outline-warning flex-fill">Edit</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="p-3">
        {{ $ownedTeams->links() }}
    </div>
@endif
