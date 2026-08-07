<span id="admin-team-maturity-total-value" class="d-none">{{ $maturity->total() }}</span>

<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
            <tr>
                <x-sortable-th field="team" label="Team" />
                <x-sortable-th field="owner" label="Owner" />
                <x-sortable-th field="overall" label="Score" class="text-center" />
                <x-sortable-th field="trend" label="Trend" class="text-center" />
                <th>Weakest Category</th>
                <x-sortable-th field="participation" label="Participation" class="text-center" />
                <x-sortable-th field="lastAssessment" label="Last Assessment" />
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($maturity as $entry)
                <tr>
                    <td>
                        <strong>{{ $entry['team']->name }}</strong>
                        @if($entry['isStale'])
                            <span class="badge bg-warning text-dark ms-1" title="Last assessment closed over 90 days ago">
                                <i class="fas fa-clock"></i> Stale
                            </span>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $entry['team']->owner->name ?? '—' }}</td>
                    <td class="text-center">
                        @if($entry['overall'] !== null)
                            @php
                                $scoreClass = $entry['overall'] >= 3 ? 'bg-success' : ($entry['overall'] >= 2 ? 'bg-warning text-dark' : 'bg-danger');
                            @endphp
                            <span class="badge {{ $scoreClass }}">{{ number_format($entry['overall'], 1) }} / 4</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($entry['trend'] === 'up')
                            <i class="fas fa-arrow-up text-success" title="Improved since previous assessment"></i>
                        @elseif($entry['trend'] === 'down')
                            <i class="fas fa-arrow-down text-danger" title="Declined since previous assessment"></i>
                        @elseif($entry['trend'] === 'flat')
                            <i class="fas fa-arrow-right text-muted" title="No significant change"></i>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($entry['weakestCategory'])
                            <span class="text-danger">{{ $entry['weakestCategory']['name'] }}</span>
                            <span class="text-muted small">({{ number_format($entry['weakestCategory']['score'], 1) }})</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($entry['participation'] !== null)
                            {{ $entry['participation'] }}%
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted small">
                        {{ $entry['assessment']?->updated_at?->format('M j, Y') ?? 'No completed assessments' }}
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.teams.show', $entry['team']->id) }}" class="btn btn-sm btn-outline-secondary">Team</a>
                        @if($entry['assessment'])
                            <a href="{{ route('user.assessments.results', $entry['assessment']) }}" class="btn btn-sm btn-outline-primary">Results</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No teams found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="p-3">
    {{ $maturity->links() }}
</div>
