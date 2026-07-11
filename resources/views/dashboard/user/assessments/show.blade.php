@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- Flash messages --}}
    @foreach (['success', 'error', 'info'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-1">{{ $assessment->title }}</h2>
            <p class="text-muted mb-0">
                <i class="bi bi-people"></i> {{ $assessment->team->name }}
                @if ($assessment->description)
                    &mdash; {{ $assessment->description }}
                @endif
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('user.assessments.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back
            </a>
            @if ($isOwner)
                @if ($assessment->isDraft())
                    <form method="POST" action="{{ route('user.assessments.publish', $assessment) }}">
                        @csrf
                        <button class="btn btn-success btn-sm" id="publishBtn"
                                {{ $assessment->questions->count() === 0 ? 'disabled' : '' }}
                                onclick="return confirm('Publish this assessment? Team members will be able to take it.')">
                            <i class="bi bi-send"></i> Publish
                        </button>
                    </form>
                @elseif ($assessment->isActive())
                    <form method="POST" action="{{ route('user.assessments.close', $assessment) }}">
                        @csrf
                        <button class="btn btn-dark btn-sm"
                                onclick="return confirm('Close this assessment? Results will become available.')">
                            <i class="bi bi-lock"></i> Close &amp; View Results
                        </button>
                    </form>
                @endif

                @if ($assessment->isDraft())
                    <form method="POST" action="{{ route('user.assessments.destroy', $assessment) }}">
                        @csrf @method('DELETE')
                        <button class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('Delete this assessment? This cannot be undone.')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                @endif
            @endif
        </div>
    </div>

    @if ($isOwner && $assessment->isDraft() && $assessment->questions->count() === 0)
        <div class="alert alert-warning" id="noQuestionsWarning">
            <i class="bi bi-exclamation-triangle"></i>
            Add at least one question before publishing.
        </div>
    @endif

    <div class="row g-4">

        {{-- Left column: questions --}}
        <div class="col-lg-7">

            {{-- Selected Questions --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-list-check"></i> Assessment Questions (<span id="selectedQuestionsCount">{{ $assessment->questions->count() }}</span>)</strong>
                    @if ($isOwner && $assessment->isDraft())
                        <span class="text-muted small">Add from the library below</span>
                    @endif
                </div>
                <div class="card-body p-0" id="selectedQuestionsBody"
                     data-remove-url-template="{{ route('user.assessments.questions.remove', [$assessment, '__ID__']) }}">
                    @if ($assessment->questions->isEmpty())
                        <p class="text-muted p-3 mb-0" id="selectedQuestionsEmpty">No questions added yet.</p>
                    @else
                        <ul class="list-group list-group-flush" id="selectedQuestionsList">
                            @foreach ($assessment->questions as $aq)
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div>
                                    <span class="badge bg-secondary me-2">{{ $loop->iteration }}</span>
                                    {{ $aq->question->text }}
                                    <br>
                                    <small class="text-muted">{{ $aq->question->category->name ?? '—' }}</small>
                                </div>
                                @if ($isOwner && $assessment->isDraft())
                                    <form method="POST" action="{{ route('user.assessments.questions.remove', [$assessment, $aq->question_id]) }}">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger ms-2">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </form>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Generate with AI (draft only, plan-gated) --}}
            @if ($isOwner && $assessment->isDraft() && auth()->user()->planHasFeature('ai-question-generation'))
            <div class="card shadow-sm mb-4 border-primary" id="aiPanel"
                 data-generate-url="{{ route('user.assessments.questions.generate-ai', $assessment) }}"
                 data-create-url="{{ route('user.assessments.questions.create-ai', $assessment) }}">
                <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
                    <i class="bi bi-robot"></i>
                    <strong>Generate with AI</strong>
                    <span class="badge bg-light text-primary ms-1">Beta</span>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Describe what you want to assess and AI will generate a question with a full 0–4 maturity rubric. Review and edit before it's added.</p>

                    <div class="row g-2">
                        <div class="col-md-8">
                            <label class="form-label small fw-semibold">What do you want to assess?</label>
                            <textarea id="aiDescription" class="form-control form-control-sm" rows="2"
                                placeholder="e.g. How well the team manages technical debt and keeps the codebase clean"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Category context</label>
                            <select id="aiCategory" class="form-select form-select-sm">
                                <option value="">— None —</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->name }}" data-id="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="aiGenerateBtn" class="btn btn-sm btn-primary w-100 mt-2" onclick="generateAiQuestion()">
                                <i class="bi bi-robot"></i> Generate
                            </button>
                        </div>
                    </div>

                    <div id="aiLoading" class="d-none text-muted small mt-2">
                        <span class="spinner-border spinner-border-sm me-1" role="status"></span> Generating&hellip;
                    </div>
                    <div id="aiError" class="d-none text-danger small mt-2"></div>

                    {{-- Review panel: populated after generation, editable before saving --}}
                    <div id="aiReviewPanel" class="d-none mt-3 border-top pt-3">
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Question Text</label>
                            <input type="text" id="aiReviewText" class="form-control form-control-sm">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Category</label>
                            <select id="aiReviewCategory" class="form-select form-select-sm">
                                <option value="" disabled selected>Select a category</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @for ($i = 0; $i <= 4; $i++)
                            <div class="mb-2">
                                <label class="form-label small">Tip for score {{ $i }}</label>
                                <textarea id="aiReviewTip{{ $i }}" class="form-control form-control-sm" rows="1"></textarea>
                            </div>
                        @endfor
                        <div class="d-flex gap-2 mt-2">
                            <button type="button" id="aiSaveBtn" class="btn btn-sm btn-success" onclick="saveAiQuestion()">
                                <i class="bi bi-check-circle"></i> Save &amp; Add to Assessment
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cancelAiReview()">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Question Library (draft only) --}}
            @if ($isOwner && $assessment->isDraft())
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <strong><i class="bi bi-book"></i> Question Library</strong>
                    <div class="row g-2 mt-1">
                        <div class="col-7">
                            <input type="text" id="librarySearch" class="form-control form-control-sm"
                                   placeholder="Search questions...">
                        </div>
                        <div class="col-5">
                            <select id="libraryCategoryFilter" class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                @foreach ($availableQuestions->keys() as $categoryName)
                                    <option value="{{ $categoryName ?? 'Uncategorised' }}">{{ $categoryName ?? 'Uncategorised' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-check mt-2 mb-0">
                        <input type="checkbox" class="form-check-input" id="librarySelectAll">
                        <label class="form-check-label small fw-semibold" for="librarySelectAll">Select All Visible</label>
                    </div>
                </div>
                <div class="card-body p-0" id="libraryBody"
                     data-add-url="{{ route('user.assessments.questions.add', $assessment) }}"
                     style="max-height: 500px; overflow-y: auto;">
                    @forelse ($availableQuestions as $categoryName => $questions)
                        @php $catKey = $categoryName ?? 'Uncategorised'; $catId = 'cat-' . \Illuminate\Support\Str::slug($catKey); @endphp
                        <div class="library-category" data-category="{{ $catKey }}">
                            <button class="btn btn-light w-100 text-start px-3 py-2 border-bottom d-flex justify-content-between align-items-center rounded-0"
                                    type="button" data-bs-toggle="collapse" data-bs-target="#{{ $catId }}">
                                <strong class="text-primary small text-uppercase">{{ $catKey }}</strong>
                                <span class="badge bg-secondary category-count">{{ $questions->count() }}</span>
                            </button>
                            <div class="collapse" id="{{ $catId }}">
                                @foreach ($questions as $question)
                                <div class="library-item border-bottom px-3 py-2 d-flex align-items-center" data-question-id="{{ $question->id }}">
                                    <input type="checkbox" class="form-check-input library-checkbox flex-shrink-0 me-2" value="{{ $question->id }}">
                                    <span class="small flex-grow-1">{{ $question->text }}</span>
                                    <button type="button" class="btn btn-sm btn-outline-primary text-nowrap ms-2 library-add-single flex-shrink-0" data-question-id="{{ $question->id }}">
                                        <i class="bi bi-plus"></i> Add
                                    </button>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-muted p-3 mb-0" id="libraryEmptyMsg">All available questions have been added.</p>
                    @endforelse
                </div>
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <span class="text-muted small" id="librarySelectedCount">0 selected</span>
                    <button type="button" class="btn btn-sm btn-primary" id="libraryAddSelected" disabled>
                        <i class="bi bi-plus-circle"></i> Add Selected
                    </button>
                </div>
            </div>
            @endif

        </div>

        {{-- Right column: evaluators + completion --}}
        <div class="col-lg-5">

            {{-- Team Maturity --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <strong><i class="bi bi-radar"></i> Team Maturity</strong>
                    @if ($maturity['overall'] !== null)
                        @php
                            $maturityScoreClass = $maturity['overall'] >= 3 ? 'bg-success' : ($maturity['overall'] >= 2 ? 'bg-warning text-dark' : 'bg-danger');
                        @endphp
                        <span class="badge {{ $maturityScoreClass }}">{{ number_format($maturity['overall'], 1) }} / 4</span>
                    @endif
                </div>
                <div class="card-body">
                    @if ($maturity['assessment'])
                        <canvas class="maturity-radar" height="200"
                                data-categories="{{ json_encode($maturity['categories']) }}"
                                data-scores="{{ json_encode($maturity['scores']) }}"></canvas>
                        <p class="text-muted small mt-2 mb-0">
                            From "{{ $maturity['assessment']->title }}" &middot; closed {{ $maturity['assessment']->updated_at->format('M j, Y') }}
                        </p>
                    @else
                        <p class="text-muted mb-0">This team hasn't completed an assessment yet.</p>
                    @endif
                </div>
            </div>

            {{-- Team Members (participation toggle) --}}
            @if ($isOwner && ($assessment->isDraft() || $assessment->isActive()) && $teamMembers->isNotEmpty())
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <strong><i class="bi bi-people"></i> Team Members</strong>
                    <span class="text-muted small d-block">Choose who can take this assessment</span>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('user.assessments.participants.update', $assessment) }}">
                        @csrf
                        @method('PUT')
                        <div class="form-check mb-2 border-bottom pb-2">
                            <input type="checkbox" class="form-check-input" id="memberToggleAll">
                            <label class="form-check-label small fw-semibold" for="memberToggleAll">Toggle All</label>
                        </div>
                        <div class="mb-2" style="max-height: 220px; overflow-y: auto;">
                            @foreach ($teamMembers as $member)
                                <div class="form-check d-flex justify-content-between align-items-center">
                                    <div>
                                        <input type="checkbox" class="form-check-input member-checkbox"
                                               name="included_ids[]" value="{{ $member->id }}"
                                               id="member-{{ $member->id }}"
                                               {{ $excludedMemberIds->contains($member->id) ? '' : 'checked' }}>
                                        <label class="form-check-label small" for="member-{{ $member->id }}">{{ $member->name }}</label>
                                    </div>
                                    @if ($excludedMemberIds->contains($member->id))
                                        <span class="badge bg-secondary">Excluded</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <button class="btn btn-sm btn-primary">Save Participants</button>
                    </form>
                </div>
            </div>
            @endif

            {{-- External Evaluators --}}
            @if ($isOwner && ($assessment->isDraft() || $assessment->isActive()))
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <strong><i class="bi bi-person-plus"></i> External Evaluators</strong>
                </div>
                <div class="card-body">
                    @if ($evaluatorStats->isNotEmpty())
                        <ul class="list-group list-group-flush mb-3">
                            @foreach ($evaluatorStats as $stat)
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>{{ $stat['user']->name }}</span>
                                <div class="d-flex align-items-center gap-2">
                                    @if ($stat['completed'])
                                        <span class="badge bg-success">Submitted</span>
                                    @elseif ($stat['status'] === 'accepted')
                                        <span class="badge bg-info text-dark">In progress</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                    <form method="POST" action="{{ route('user.assessments.evaluators.remove', [$assessment, $stat['user']->id]) }}">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger py-0">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted small mb-3">No external evaluators invited yet.</p>
                    @endif

                    @if ($potentialEvaluators->isNotEmpty())
                        <form method="POST" action="{{ route('user.assessments.evaluators.invite', $assessment) }}">
                            @csrf
                            <div class="form-check mb-2 border-bottom pb-2">
                                <input type="checkbox" class="form-check-input" id="evaluatorSelectAll">
                                <label class="form-check-label small fw-semibold" for="evaluatorSelectAll">Select All</label>
                            </div>
                            <div class="mb-2" style="max-height: 220px; overflow-y: auto;">
                                @foreach ($potentialEvaluators as $u)
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input evaluator-checkbox"
                                               name="user_ids[]" value="{{ $u->id }}" id="evaluator-{{ $u->id }}">
                                        <label class="form-check-label small" for="evaluator-{{ $u->id }}">{{ $u->name }}</label>
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn btn-sm btn-primary text-nowrap">
                                <i class="bi bi-person-plus"></i> Invite Selected
                            </button>
                        </form>
                    @else
                        <p class="text-muted small mb-0">No other users available to invite.</p>
                    @endif
                </div>
            </div>
            @endif

            {{-- Completion Tracker (active/closed) --}}
            @if ($isOwner && ($assessment->isActive() || $assessment->isClosed()))
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <strong><i class="bi bi-bar-chart-steps"></i> Completion Tracker</strong>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @foreach ($completionStats as $stat)
                        <li class="list-group-item d-flex justify-content-between align-items-center {{ $stat['excluded'] ? 'opacity-50' : '' }}">
                            <span>{{ $stat['user']->name }} <small class="text-muted">(member)</small></span>
                            @if ($stat['excluded'])
                                <span class="badge bg-secondary">Excluded</span>
                            @elseif ($stat['completed'])
                                <span class="badge bg-success"><i class="bi bi-check-lg"></i> Done</span>
                            @else
                                <span class="badge bg-secondary">{{ $stat['responses'] }}/{{ $stat['total'] }}</span>
                            @endif
                        </li>
                        @endforeach
                        @foreach ($evaluatorStats as $stat)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>{{ $stat['user']->name }} <small class="text-muted">(external)</small></span>
                            @if ($stat['completed'])
                                <span class="badge bg-success"><i class="bi bi-check-lg"></i> Done</span>
                            @else
                                <span class="badge bg-secondary">{{ $stat['responses'] }}/{{ $stat['total'] }}</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            {{-- Non-owner active: take button --}}
            @if (!$isOwner && $assessment->isActive())
            <div class="card shadow-sm">
                <div class="card-body text-center py-4">
                    <i class="bi bi-clipboard2-check display-4 text-primary"></i>
                    <h5 class="mt-3">Ready to take this assessment?</h5>
                    <a href="{{ route('user.assessments.take', $assessment) }}" class="btn btn-primary mt-2">
                        Start Assessment
                    </a>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.querySelectorAll('.maturity-radar').forEach(function (canvas) {
        const categories = JSON.parse(canvas.dataset.categories);
        const scores = JSON.parse(canvas.dataset.scores);
        const mini = canvas.dataset.mini === 'true';

        // A radar needs 3+ axes to read as a shape — with fewer, Chart.js
        // just draws an overlapping line, so fall back to a bar chart.
        if (categories.length < 3) {
            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: categories,
                    datasets: [{
                        label: 'Current Maturity',
                        data: scores,
                        backgroundColor: 'rgba(13, 110, 253, 0.6)',
                    }],
                },
                options: {
                    indexAxis: 'y',
                    scales: {
                        x: { min: 0, max: 4, ticks: { stepSize: 1 } },
                        y: { ticks: { display: !mini } },
                    },
                    plugins: { legend: { display: false } },
                },
            });
            return;
        }

        new Chart(canvas, {
            type: 'radar',
            data: {
                labels: categories,
                datasets: [{
                    label: 'Current Maturity',
                    data: scores,
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                }],
            },
            options: {
                scales: {
                    r: {
                        min: 0, max: 4, ticks: { stepSize: 1, display: !mini },
                        pointLabels: { display: !mini, font: { size: 11 } },
                    },
                },
                plugins: { legend: { display: false } },
            },
        });
    });

    document.getElementById('evaluatorSelectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.evaluator-checkbox').forEach(cb => cb.checked = this.checked);
    });

    document.getElementById('memberToggleAll')?.addEventListener('change', function () {
        document.querySelectorAll('.member-checkbox').forEach(cb => cb.checked = this.checked);
    });

    // ---- Question Library: search + category filter + collapsible categories ----
    function applyLibraryFilters() {
        const term = (document.getElementById('librarySearch')?.value || '').toLowerCase();
        const cat = document.getElementById('libraryCategoryFilter')?.value || '';

        document.querySelectorAll('.library-category').forEach(catEl => {
            const matchesCategory = !cat || catEl.dataset.category === cat;
            let anyVisible = false;

            catEl.querySelectorAll('.library-item').forEach(item => {
                const text = item.querySelector('span').textContent.toLowerCase();
                const visible = matchesCategory && text.includes(term);
                item.style.display = visible ? '' : 'none';
                if (visible) anyVisible = true;
            });

            catEl.style.display = anyVisible ? '' : 'none';

            // Auto-expand a category when it has matches while actively searching/filtering,
            // so results aren't hidden behind a closed collapse.
            const collapseEl = catEl.querySelector('.collapse');
            if (collapseEl && anyVisible && (term || cat)) {
                collapseEl.classList.add('show');
            }
        });

        updateLibrarySelectedCount();
    }

    document.getElementById('librarySearch')?.addEventListener('input', applyLibraryFilters);
    document.getElementById('libraryCategoryFilter')?.addEventListener('change', applyLibraryFilters);

    // ---- Question Library: select all (visible only) ----
    document.getElementById('librarySelectAll')?.addEventListener('change', function () {
        document.querySelectorAll('.library-checkbox').forEach(cb => {
            const item = cb.closest('.library-item');
            if (item.style.display !== 'none') cb.checked = this.checked;
        });
        updateLibrarySelectedCount();
    });

    document.addEventListener('change', function (e) {
        if (e.target.classList.contains('library-checkbox')) updateLibrarySelectedCount();
    });

    function updateLibrarySelectedCount() {
        const count = document.querySelectorAll('.library-checkbox:checked').length;
        const countEl = document.getElementById('librarySelectedCount');
        const btn = document.getElementById('libraryAddSelected');
        if (countEl) countEl.textContent = count + ' selected';
        if (btn) btn.disabled = count === 0;
    }

    // ---- Question Library: add (single or bulk) via AJAX ----
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function ensureSelectedQuestionsList() {
        let list = document.getElementById('selectedQuestionsList');
        if (!list) {
            document.getElementById('selectedQuestionsEmpty')?.remove();
            list = document.createElement('ul');
            list.className = 'list-group list-group-flush';
            list.id = 'selectedQuestionsList';
            document.getElementById('selectedQuestionsBody').appendChild(list);
        }
        return list;
    }

    function showLibraryToast(message) {
        const container = document.querySelector('.container.py-4');
        const alert = document.createElement('div');
        alert.className = 'alert alert-success alert-dismissible fade show';
        alert.innerHTML = escapeHtml(message) + '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        container.insertBefore(alert, container.firstChild);
        setTimeout(() => alert.remove(), 4000);
    }

    // Shared by both "add from library" and "generate with AI" — updates the
    // Assessment Questions list, the library, counts, and the publish button.
    function applyAddedQuestions(data) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const removeUrlTemplate = document.getElementById('selectedQuestionsBody').dataset.removeUrlTemplate;
        const libraryBody = document.getElementById('libraryBody');
        const list = ensureSelectedQuestionsList();

        data.added.forEach(q => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-start';
            li.innerHTML = `
                <div>
                    <span class="badge bg-secondary me-2">${list.children.length + 1}</span>
                    ${escapeHtml(q.text)}
                    <br>
                    <small class="text-muted">${escapeHtml(q.category)}</small>
                </div>
                <form method="POST" action="${removeUrlTemplate.replace('__ID__', q.question_id)}">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button class="btn btn-sm btn-outline-danger ms-2"><i class="bi bi-x"></i></button>
                </form>
            `;
            list.appendChild(li);

            const libItem = document.querySelector('.library-item[data-question-id="' + q.question_id + '"]');
            if (libItem) {
                const catEl = libItem.closest('.library-category');
                libItem.remove();
                if (catEl) {
                    const remaining = catEl.querySelectorAll('.library-item').length;
                    const countBadge = catEl.querySelector('.category-count');
                    if (countBadge) countBadge.textContent = remaining;
                    if (remaining === 0) catEl.remove();
                }
            }
        });

        document.getElementById('selectedQuestionsCount').textContent = data.total;
        document.getElementById('publishBtn')?.removeAttribute('disabled');
        document.getElementById('noQuestionsWarning')?.remove();

        if (libraryBody && document.querySelectorAll('.library-category').length === 0) {
            libraryBody.innerHTML = '<p class="text-muted p-3 mb-0" id="libraryEmptyMsg">All available questions have been added.</p>';
        }

        const selectAll = document.getElementById('librarySelectAll');
        if (selectAll) selectAll.checked = false;
        updateLibrarySelectedCount();
    }

    async function addQuestionsToAssessment(questionIds) {
        const libraryBody = document.getElementById('libraryBody');
        const addUrl = libraryBody.dataset.addUrl;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        let response;
        try {
            response = await fetch(addUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ question_ids: questionIds }),
            });
        } catch (err) {
            alert('Could not reach the server. Please check your connection and try again.');
            return;
        }

        if (!response.ok) {
            alert('Something went wrong adding the question(s). Please try again.');
            return;
        }

        const data = await response.json();
        applyAddedQuestions(data);
        showLibraryToast(data.added.length + ' question' + (data.added.length === 1 ? '' : 's') + ' added.');
    }

    document.getElementById('libraryAddSelected')?.addEventListener('click', function () {
        const ids = Array.from(document.querySelectorAll('.library-checkbox:checked')).map(cb => cb.value);
        if (ids.length) addQuestionsToAssessment(ids);
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.library-add-single');
        if (btn) addQuestionsToAssessment([btn.dataset.questionId]);
    });

    // ---- Generate with AI ----
    async function generateAiQuestion() {
        const panel = document.getElementById('aiPanel');
        if (!panel) return;

        const description = document.getElementById('aiDescription').value.trim();
        const categorySelect = document.getElementById('aiCategory');
        const category = categorySelect.value;
        const categoryId = categorySelect.selectedOptions[0]?.dataset.id || '';
        const btn = document.getElementById('aiGenerateBtn');
        const loading = document.getElementById('aiLoading');
        const errorEl = document.getElementById('aiError');

        errorEl.classList.add('d-none');

        if (description.length < 10) {
            errorEl.textContent = 'Please describe what you want to assess (at least 10 characters).';
            errorEl.classList.remove('d-none');
            return;
        }

        btn.disabled = true;
        loading.classList.remove('d-none');

        try {
            const res = await fetch(panel.dataset.generateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ description, category }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Unknown error');

            document.getElementById('aiReviewText').value = data.text ?? '';
            for (let i = 0; i <= 4; i++) {
                document.getElementById('aiReviewTip' + i).value = data['tip_' + i] ?? '';
            }
            if (categoryId) {
                document.getElementById('aiReviewCategory').value = categoryId;
            }
            document.getElementById('aiReviewPanel').classList.remove('d-none');
        } catch (err) {
            errorEl.textContent = err.message || 'Something went wrong. Please try again.';
            errorEl.classList.remove('d-none');
        } finally {
            btn.disabled = false;
            loading.classList.add('d-none');
        }
    }

    async function saveAiQuestion() {
        const panel = document.getElementById('aiPanel');
        if (!panel) return;

        const text = document.getElementById('aiReviewText').value.trim();
        const categoryId = document.getElementById('aiReviewCategory').value;
        const errorEl = document.getElementById('aiError');
        errorEl.classList.add('d-none');

        if (!text || !categoryId) {
            errorEl.textContent = 'Question text and category are required.';
            errorEl.classList.remove('d-none');
            return;
        }

        const payload = { text: text, category_id: categoryId };
        for (let i = 0; i <= 4; i++) {
            payload['tip_' + i] = document.getElementById('aiReviewTip' + i).value;
        }

        const saveBtn = document.getElementById('aiSaveBtn');
        saveBtn.disabled = true;

        let response;
        try {
            response = await fetch(panel.dataset.createUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify(payload),
            });
        } catch (err) {
            errorEl.textContent = 'Could not reach the server. Please try again.';
            errorEl.classList.remove('d-none');
            saveBtn.disabled = false;
            return;
        }

        if (!response.ok) {
            const data = await response.json().catch(() => ({}));
            errorEl.textContent = data.error || 'Could not save the question.';
            errorEl.classList.remove('d-none');
            saveBtn.disabled = false;
            return;
        }

        const data = await response.json();
        applyAddedQuestions(data);
        cancelAiReview();
        document.getElementById('aiDescription').value = '';
        showLibraryToast('Question generated and added to the assessment.');
        saveBtn.disabled = false;
    }

    function cancelAiReview() {
        document.getElementById('aiReviewPanel')?.classList.add('d-none');
    }
</script>
@endpush
