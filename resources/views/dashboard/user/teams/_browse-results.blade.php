<span id="browse-teams-total-value" class="d-none">{{ $teams->total() }}</span>

@if ($teams->isEmpty())
    <p class="text-muted p-3 mb-0">No teams found. Ask a team owner to open their team to join requests, or check back later.</p>
@else
    {{-- Table view --}}
    <div class="table-responsive browse-teams-view-table">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <x-sortable-th field="name" label="Name" />
                    <th>Owner</th>
                    <th>Framework</th>
                    <th class="text-center">Members</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($teams as $team)
                <tr>
                    <td>
                        <strong>{{ $team->name }}</strong>
                        @if($team->description)
                            <br><small class="text-muted">{{ Str::limit($team->description, 60) }}</small>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $team->owner->name ?? '—' }}</td>
                    <td class="text-muted small">{{ $team->team_frameq->name ?? '—' }}</td>
                    <td class="text-center">{{ $team->members->count() }}</td>
                    <td class="text-end">
                        @if ($pendingTeamIds->contains($team->id))
                            <span class="badge bg-warning text-dark">Request Pending</span>
                        @else
                            <form method="POST" action="{{ route('user.teams.join-requests.store', $team) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-primary">Request to Join</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Card view --}}
    <div class="row g-4 p-3 browse-teams-view-cards d-none">
        @foreach ($teams as $team)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow border-0 browse-team-card">
                <div class="card-header bg-primary text-white d-flex align-items-center gap-2 py-3">
                    <i class="fas fa-users fa-lg"></i>
                    <h5 class="mb-0 fw-bold text-truncate">{{ $team->name }}</h5>
                </div>
                <div class="card-body">
                    @if ($team->description)
                        <p class="text-muted mb-3">{{ Str::limit($team->description, 80) }}</p>
                    @endif
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-crown me-1"></i> {{ $team->owner->name ?? '—' }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-diagram-project me-1"></i> {{ $team->team_frameq->name ?? '—' }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-user-friends me-1"></i> {{ $team->members->count() }} member{{ $team->members->count() === 1 ? '' : 's' }}
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 pb-3">
                    @if ($pendingTeamIds->contains($team->id))
                        <span class="badge bg-warning text-dark w-100 py-2">Request Pending</span>
                    @else
                        <form method="POST" action="{{ route('user.teams.join-requests.store', $team) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">Request to Join</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="p-3">
        {{ $teams->links() }}
    </div>
@endif
