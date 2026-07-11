@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="mb-4">
                <h2 class="mb-1">{{ $assessment->title }}</h2>
                <p class="text-muted">
                    <i class="bi bi-people"></i> {{ $assessment->team->name }}
                    @if ($respondentType === 'external')
                        &mdash; <span class="badge bg-info text-dark">External Evaluator</span>
                    @endif
                </p>
                @if ($assessment->description)
                    <p>{{ $assessment->description }}</p>
                @endif
            </div>

            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i>
                Score each practice on a scale of <strong>0 (not doing it at all)</strong> to <strong>4 (fully embedded)</strong>. Read the descriptions below each question to help pick the right score.
            </div>

            <form method="POST" action="{{ route('user.assessments.responses.store', $assessment) }}">
                @csrf
                <input type="hidden" name="respondent_type" value="{{ $respondentType }}">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                @endif

                @foreach ($assessment->questions as $index => $aq)
                @php $q = $aq->question; $existing = $existingResponses[$q->id] ?? null; @endphp
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between">
                        <strong>{{ $loop->iteration }}. {{ $q->text }}</strong>
                        <span class="badge bg-secondary">{{ $q->category->name ?? '—' }}</span>
                    </div>
                    <div class="card-body">

                        {{-- Score radio buttons --}}
                        <div class="d-flex gap-2 flex-wrap mb-3">
                            @for ($i = 0; $i <= 4; $i++)
                            <div class="flex-fill text-center">
                                <input type="radio" class="btn-check score-radio"
                                       name="responses[{{ $q->id }}]"
                                       id="q{{ $q->id }}_s{{ $i }}"
                                       value="{{ $i }}"
                                       data-target="rubric-{{ $q->id }}"
                                       {{ $existing == $i ? 'checked' : '' }}
                                       required>
                                <label class="btn btn-outline-primary w-100" for="q{{ $q->id }}_s{{ $i }}">
                                    {{ $i }}
                                </label>
                            </div>
                            @endfor
                        </div>

                        {{-- Always-visible rubric --}}
                        <div id="rubric-{{ $q->id }}" class="rubric-list mb-3">
                            @for ($i = 0; $i <= 4; $i++)
                            <div class="rubric-row d-flex gap-2 p-2 border-bottom {{ $existing == $i ? 'bg-primary-subtle' : '' }}"
                                 data-score="{{ $i }}">
                                <span class="badge bg-secondary">{{ $i }}</span>
                                <span class="{{ $q->{'tip_' . $i} ? '' : 'text-muted fst-italic' }}">
                                    {{ $q->{'tip_' . $i} ?: 'No description provided.' }}
                                </span>
                            </div>
                            @endfor
                        </div>

                        {{-- Comment --}}
                        <div>
                            <label class="form-label small text-muted">Comment <span class="text-muted">(optional)</span></label>
                            <textarea name="comments[{{ $q->id }}]" class="form-control form-control-sm" rows="2"
                                      placeholder="Any context or notes..."></textarea>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="d-flex justify-content-between">
                    <a href="{{ route('user.assessments.show', $assessment) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button type="submit" class="btn btn-primary px-4">
                        Submit Responses <i class="bi bi-check-circle"></i>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.score-radio').forEach(function (radio) {
        radio.addEventListener('change', function () {
            const rubric = document.getElementById(this.dataset.target);
            if (!rubric) return;
            rubric.querySelectorAll('.rubric-row').forEach(function (row) {
                row.classList.toggle('bg-primary-subtle', row.dataset.score === radio.value);
            });
        });
    });
</script>
@endpush
