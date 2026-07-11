@extends('layouts.app')

@section('content')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-1">{{ $assessment->title }} — Results</h2>
            <p class="text-muted mb-0"><i class="bi bi-people"></i> {{ $assessment->team->name }}</p>
        </div>
        <a href="{{ route('user.assessments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    {{-- Score summary cards --}}
    @php $summaryColClass = $hasExternalData ? 'col-md-3' : 'col-md-4'; @endphp
    <div class="row g-3 mb-4">
        <div class="{{ $summaryColClass }}">
            <div class="card text-center border-primary shadow-sm">
                <div class="card-body py-3">
                    <div class="display-5 fw-bold text-primary">{{ number_format($overallTeamScore, 1) }}<small class="fs-5">/4</small></div>
                    <div class="text-muted small mt-1">Team Average Score</div>
                </div>
            </div>
        </div>
        @if ($hasExternalData)
        <div class="{{ $summaryColClass }}">
            <div class="card text-center border-warning shadow-sm">
                <div class="card-body py-3">
                    <div class="display-5 fw-bold text-warning">{{ number_format($overallExternalScore, 1) }}<small class="fs-5">/4</small></div>
                    <div class="text-muted small mt-1">External Evaluator Average</div>
                </div>
            </div>
        </div>
        @endif
        <div class="{{ $summaryColClass }}">
            <div class="card text-center border-dark shadow-sm">
                <div class="card-body py-3">
                    @if ($orgAverage !== null)
                        <div class="display-5 fw-bold text-dark">{{ number_format($orgAverage, 1) }}<small class="fs-5">/4</small></div>
                        <div class="text-muted small mt-1">Org Average</div>
                        @php $orgDiff = round($overallTeamScore - $orgAverage, 2); @endphp
                        @if ($orgDiff > 0.05)
                            <div class="small text-success mt-1"><i class="fas fa-arrow-up"></i> {{ number_format(abs($orgDiff), 1) }} above</div>
                        @elseif ($orgDiff < -0.05)
                            <div class="small text-danger mt-1"><i class="fas fa-arrow-down"></i> {{ number_format(abs($orgDiff), 1) }} below</div>
                        @else
                            <div class="small text-muted mt-1">About average</div>
                        @endif
                    @else
                        <div class="display-5 fw-bold text-dark">—</div>
                        <div class="text-muted small mt-1">Org Average</div>
                        <div class="small text-muted mt-1">No org-wide data yet</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="{{ $summaryColClass }}">
            <div class="card text-center border-secondary shadow-sm">
                <div class="card-body py-3">
                    <div class="display-5 fw-bold text-secondary">{{ $assessment->questions->count() }}</div>
                    <div class="text-muted small mt-1">Questions</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Radar Chart --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <strong><i class="bi bi-diagram-3"></i> Maturity Radar</strong>
            @if ($individualScores->isNotEmpty())
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="toggleIndividual">
                    <label class="form-check-label small" for="toggleIndividual">Show individual scores</label>
                </div>
            @endif
        </div>
        <div class="card-body d-flex justify-content-center">
            <div style="max-width: 550px; width: 100%;">
                <canvas id="radarChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Per-category breakdown table --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-light"><strong><i class="bi bi-table"></i> Category Breakdown</strong></div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Category</th>
                        <th class="text-center">Team Avg</th>
                        @if ($hasExternalData)
                        <th class="text-center">Evaluator Avg</th>
                        @endif
                        <th class="text-center">Org Avg</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $i => $cat)
                    <tr>
                        <td>{{ $cat }}</td>
                        <td class="text-center">
                            <span class="badge bg-primary">{{ number_format($memberData[$i], 1) }}</span>
                        </td>
                        @if ($hasExternalData)
                        <td class="text-center">
                            <span class="badge bg-warning text-dark">{{ number_format($externalData[$i], 1) }}</span>
                        </td>
                        @endif
                        <td class="text-center">
                            @if ($orgCategoryData[$i] !== null)
                                <span class="badge bg-dark">{{ number_format($orgCategoryData[$i], 1) }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
    const labels = @json($categories);
    const memberData = @json($memberData);
    const externalData = @json($externalData);
    const hasExternal = @json($hasExternalData);
    const individualScores = @json($individualScores);

    const palette = [
        'rgba(108,117,125,0.4)', 'rgba(23,162,184,0.4)', 'rgba(40,167,69,0.4)',
        'rgba(255,193,7,0.4)', 'rgba(220,53,69,0.4)', 'rgba(111,66,193,0.4)',
    ];

    const baseDatasets = [
        {
            label: 'Team Average',
            data: memberData,
            backgroundColor: 'rgba(13,110,253,0.15)',
            borderColor: 'rgba(13,110,253,1)',
            borderWidth: 2,
            pointBackgroundColor: 'rgba(13,110,253,1)',
        },
    ];

    if (hasExternal) {
        baseDatasets.push({
            label: 'External Evaluators',
            data: externalData,
            backgroundColor: 'rgba(255,193,7,0.15)',
            borderColor: 'rgba(255,163,0,1)',
            borderWidth: 2,
            borderDash: [5, 5],
            pointBackgroundColor: 'rgba(255,163,0,1)',
        });
    }

    const individualDatasets = individualScores.map((member, i) => ({
        label: member.name,
        data: member.scores,
        backgroundColor: 'transparent',
        borderColor: palette[i % palette.length].replace('0.4', '0.7'),
        borderWidth: 1,
        borderDash: [3, 3],
        pointRadius: 3,
        hidden: true,
    }));

    const chart = new Chart(document.getElementById('radarChart'), {
        type: 'radar',
        data: { labels, datasets: [...baseDatasets, ...individualDatasets] },
        options: {
            scales: {
                r: {
                    min: 0,
                    max: 4,
                    ticks: { stepSize: 1 },
                    pointLabels: { font: { size: 13 } },
                }
            },
            plugins: {
                legend: { position: 'bottom' },
            },
        },
    });

    document.getElementById('toggleIndividual')?.addEventListener('change', function () {
        const show = this.checked;
        chart.data.datasets.forEach((ds, i) => {
            if (i >= baseDatasets.length) {
                ds.hidden = !show;
            }
        });
        chart.update();
    });
</script>
@endpush
