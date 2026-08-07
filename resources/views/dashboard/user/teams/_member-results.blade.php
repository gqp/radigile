<span id="member-teams-total-value" class="d-none">{{ $memberTeams->total() }}</span>

@if ($memberTeams->isEmpty())
    <p class="text-muted p-3 mb-0">You haven't been added to any teams yet.</p>
@else
    {{-- Table view --}}
    <div class="table-responsive teams-view-table">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <x-sortable-th field="name" label="Name" sort-param="member_sort" dir-param="member_dir" page-param="member_page" />
                    <th class="text-center">Maturity</th>
                    <th>Owner</th>
                    <th>Framework</th>
                    <th>My Role</th>
                    <th>Joined</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($memberTeams as $team)
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
                    <td class="text-muted small">{{ $team->owner->name ?? '—' }}</td>
                    <td class="text-muted small">{{ $team->team_frameq->name ?? '—' }}</td>
                    <td><span class="badge bg-secondary">{{ ucfirst($team->pivot->role ?? 'member') }}</span></td>
                    <td class="text-muted small">{{ $team->pivot->created_at?->format('d M Y') ?? '—' }}</td>
                    <td class="text-end">
                        <a href="{{ route('user.teams.show', $team->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Card view --}}
    <div class="row g-4 p-3 teams-view-cards d-none">
        @foreach ($memberTeams as $team)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow border-0 team-card">
                <div class="card-header bg-secondary text-white d-flex align-items-center gap-2 py-3">
                    <i class="fas fa-user-friends fa-lg"></i>
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
                            <i class="fas fa-crown me-1"></i> {{ $team->owner->name ?? '—' }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-diagram-project me-1"></i> {{ $team->team_frameq->name ?? '—' }}
                        </span>
                        <span class="badge bg-info text-dark px-3 py-2">
                            {{ ucfirst($team->pivot->role ?? 'member') }}
                        </span>
                    </div>
                    <p class="text-muted small mb-0"><i class="fas fa-calendar me-1"></i> Joined {{ $team->pivot->created_at?->format('d M Y') ?? '—' }}</p>
                </div>
                <div class="card-footer bg-white border-top-0 pb-3">
                    <a href="{{ route('user.teams.show', $team->id) }}" class="btn btn-primary w-100">View</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="p-3">
        {{ $memberTeams->links() }}
    </div>
@endif
