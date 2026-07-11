@php
    $entry = $maturityByTeamId->get($team->id);
    $chartSize = $size ?? 60;
@endphp
@if ($entry && $entry['assessment'])
    <canvas class="maturity-radar" data-mini="true" width="{{ $chartSize }}" height="{{ $chartSize }}"
            style="width: {{ $chartSize }}px; height: {{ $chartSize }}px;"
            data-categories="{{ json_encode($entry['categories']) }}"
            data-scores="{{ json_encode($entry['scores']) }}"></canvas>
@else
    <span class="text-muted small">No data</span>
@endif
