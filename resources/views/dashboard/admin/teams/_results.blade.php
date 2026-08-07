<span id="admin-teams-total-value" class="d-none">{{ $teams->total() }}</span>

@if ($teams->isEmpty())
    <p class="text-muted p-3 mb-0">No teams found.</p>
@else
    {{-- Table view --}}
    <div class="table-responsive admin-teams-view-table">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <x-sortable-th field="name" label="Name" />
                    <th>Description</th>
                    <th>Privacy</th>
                    <th>Framework</th>
                    <th>Domain</th>
                    <th>Owner</th>
                    <th>Members</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($teams as $team)
                <tr>
                    <td><strong>{{ $team->name }}</strong></td>
                    <td class="text-muted small">{{ $team->description }}</td>
                    <td>{{ ucfirst($team->type) }}</td>
                    <td class="text-muted small">{{ $team->framework ?? 'N/A' }}</td>
                    <td class="text-muted small">{{ ucfirst($team->team_domain) }}</td>
                    <td>{{ $team->owner->name }}</td>
                    <td class="text-center">{{ $team->members->count() }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.teams.show', $team->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                        <a href="{{ route('admin.teams.edit', $team->id) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                        <form action="{{ route('admin.teams.destroy', $team->id) }}" method="POST" style="display:inline;" onsubmit="return confirmDeleteTeam()">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Card view --}}
    <div class="row g-4 p-3 admin-teams-view-cards d-none">
        @foreach ($teams as $team)
        <div class="col-md-6 col-xl-4">
            <div class="card h-100 shadow border-0 admin-team-card">
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
                            <i class="fas fa-crown me-1"></i> {{ $team->owner->name }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="fas fa-user-friends me-1"></i> {{ $team->members->count() }} member{{ $team->members->count() === 1 ? '' : 's' }}
                        </span>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            {{ ucfirst($team->type) }}
                        </span>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 d-flex gap-2 pb-3">
                    <a href="{{ route('admin.teams.show', $team->id) }}" class="btn btn-outline-secondary flex-fill">View</a>
                    <a href="{{ route('admin.teams.edit', $team->id) }}" class="btn btn-outline-warning flex-fill">Edit</a>
                    <form action="{{ route('admin.teams.destroy', $team->id) }}" method="POST" class="flex-fill" onsubmit="return confirmDeleteTeam()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="p-3">
        {{ $teams->links() }}
    </div>
@endif
