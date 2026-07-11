@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-satellite-dish"></i> Team Maturity Report</h4>
        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
            <strong>{{ $maturity->count() }} team{{ $maturity->count() === 1 ? '' : 's' }}</strong>
            <input type="text" id="maturity-search" class="form-control form-control-sm" style="max-width: 260px;"
                   placeholder="Search teams...">
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="maturity-table">
                <thead class="table-light">
                    <tr>
                        <th data-sort="team" style="cursor:pointer">Team <i class="fas fa-sort text-muted small"></i></th>
                        <th data-sort="owner" style="cursor:pointer">Owner <i class="fas fa-sort text-muted small"></i></th>
                        <th data-sort="overall" class="text-center" style="cursor:pointer">Score <i class="fas fa-sort text-muted small"></i></th>
                        <th data-sort="trend" class="text-center" style="cursor:pointer">Trend <i class="fas fa-sort text-muted small"></i></th>
                        <th>Weakest Category</th>
                        <th data-sort="participation" class="text-center" style="cursor:pointer">Participation <i class="fas fa-sort text-muted small"></i></th>
                        <th data-sort="lastAssessment" style="cursor:pointer">Last Assessment <i class="fas fa-sort text-muted small"></i></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($maturity as $entry)
                        <tr class="maturity-row"
                            data-team="{{ strtolower($entry['team']->name) }}"
                            data-owner="{{ strtolower($entry['team']->owner->name ?? '') }}"
                            data-overall="{{ $entry['overall'] ?? -1 }}"
                            data-trend="{{ $entry['trend'] ?? 'zzz' }}"
                            data-participation="{{ $entry['participation'] ?? -1 }}"
                            data-last-assessment="{{ $entry['assessment']?->updated_at?->timestamp ?? 0 }}">
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
                        <tr><td colspan="8" class="text-center text-muted py-4">No teams yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.getElementById('maturity-search')?.addEventListener('input', function (e) {
        const term = e.target.value.trim().toLowerCase();
        document.querySelectorAll('#maturity-table tbody tr.maturity-row').forEach(function (row) {
            const matches = row.dataset.team.includes(term) || row.dataset.owner.includes(term);
            row.classList.toggle('d-none', !matches);
        });
    });

    document.querySelectorAll('#maturity-table th[data-sort]').forEach(function (th) {
        let ascending = true;
        th.addEventListener('click', function () {
            const attr = 'data-' + th.dataset.sort.replace(/([A-Z])/g, '-$1').toLowerCase();
            const tbody = document.querySelector('#maturity-table tbody');
            const rows = Array.from(tbody.querySelectorAll('tr.maturity-row'));

            rows.sort(function (a, b) {
                const aVal = a.getAttribute(attr);
                const bVal = b.getAttribute(attr);
                const aNum = parseFloat(aVal);
                const bNum = parseFloat(bVal);
                const bothNumeric = !isNaN(aNum) && !isNaN(bNum);
                const cmp = bothNumeric ? (aNum - bNum) : aVal.localeCompare(bVal);
                return ascending ? cmp : -cmp;
            });

            rows.forEach(row => tbody.appendChild(row));
            ascending = !ascending;
        });
    });
</script>
@endpush
