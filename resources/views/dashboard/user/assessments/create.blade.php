@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-clipboard-plus"></i> Create Assessment</h4>
                    <a href="{{ route('user.assessments.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($teams->isEmpty())
                        <div class="alert alert-warning">
                            You need to <a href="{{ route('user.teams.create') }}">create a team</a> before you can create an assessment.
                        </div>
                    @else
                        <form method="POST" action="{{ route('user.assessments.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="team_id" class="form-label">Team</label>
                                <select name="team_id" id="team_id" class="form-select @error('team_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select a team</option>
                                    @foreach ($teams as $team)
                                        <option value="{{ $team->id }}" {{ (old('team_id') ?? request('team_id')) == $team->id ? 'selected' : '' }}>
                                            {{ $team->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            @if ($templates->isNotEmpty() || $aiGenerationEnabled)
                                <div class="mb-4" id="template-picker-section" style="display:none;">
                                    <label class="form-label">How do you want to start? <small class="text-muted">(optional)</small></label>
                                    <input type="hidden" name="template_id" id="template_id" value="{{ old('template_id') }}">
                                    <input type="hidden" name="ai_generate" id="ai_generate" value="{{ old('ai_generate', '0') }}">
                                    <div id="template-picker-list" class="list-group" style="max-height: 260px; overflow-y:auto;">
                                        <button type="button" class="list-group-item list-group-item-action template-option active" data-mode="scratch" data-template-id="" data-team-id="*">
                                            Start from scratch
                                        </button>
                                        @if ($aiGenerationEnabled)
                                            <button type="button" class="list-group-item list-group-item-action template-option" data-mode="ai" data-template-id="" data-team-id="*">
                                                <i class="bi bi-stars"></i> <strong>Generate with AI</strong>
                                                <small class="text-muted d-block">Draft a full set of questions tailored to this team.</small>
                                            </button>
                                        @endif
                                        @foreach ($templates as $template)
                                            <button type="button" class="list-group-item list-group-item-action template-option"
                                                    data-mode="template"
                                                    data-template-id="{{ $template->id }}"
                                                    data-team-id="{{ $template->is_public ? '*' : $template->team_id }}"
                                                    data-title="{{ $template->title }}">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <strong>{{ $template->title }}</strong>
                                                    <span class="badge {{ $template->is_public ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ $template->is_public ? 'Public' : ($template->team->name ?? 'Team') }}
                                                    </span>
                                                </div>
                                                @if ($template->description)
                                                    <small class="text-muted d-block">{{ \Illuminate\Support\Str::limit($template->description, 100) }}</small>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>

                                    @if ($aiGenerationEnabled)
                                        <div id="aiNoteSection" class="mt-2" style="display:none;">
                                            <label for="ai_note" class="form-label small">Anything specific to focus on? <small class="text-muted">(optional)</small></label>
                                            <textarea name="ai_note" id="ai_note" rows="2" class="form-control form-control-sm"
                                                      placeholder="e.g. we just adopted Kanban and want to focus on flow">{{ old('ai_note') }}</textarea>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="mb-3">
                                <label for="title" class="form-label">Assessment Title</label>
                                <input type="text" name="title" id="title"
                                       class="form-control @error('title') is-invalid @enderror"
                                       value="{{ old('title') }}" placeholder="e.g. Q3 Team Maturity Check" required>
                                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description" class="form-label">Description <small class="text-muted">(optional)</small></label>
                                <textarea name="description" id="description" rows="3"
                                          class="form-control @error('description') is-invalid @enderror"
                                          placeholder="What is this assessment for?">{{ old('description') }}</textarea>
                                @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Create &amp; Add Questions <i class="bi bi-arrow-right"></i>
                            </button>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

@if ($templates->isNotEmpty() || $aiGenerationEnabled)
    @push('scripts')
    <script>
        (function () {
            const teamSelect = document.getElementById('team_id');
            const section = document.getElementById('template-picker-section');
            const templateIdInput = document.getElementById('template_id');
            const aiGenerateInput = document.getElementById('ai_generate');
            const aiNoteSection = document.getElementById('aiNoteSection');
            const options = document.querySelectorAll('.template-option');

            function refreshTemplatesForTeam() {
                const teamId = teamSelect.value;
                let anyVisible = false;

                options.forEach(function (option) {
                    const optionTeamId = option.dataset.teamId;
                    const visible = optionTeamId === '*' || optionTeamId === teamId;
                    option.style.display = visible ? '' : 'none';
                    if (visible) anyVisible = true;
                });

                section.style.display = (teamId && anyVisible) ? '' : 'none';
            }

            options.forEach(function (option) {
                option.addEventListener('click', function () {
                    options.forEach(function (o) { o.classList.remove('active'); });
                    option.classList.add('active');
                    templateIdInput.value = option.dataset.templateId || '';
                    if (aiGenerateInput) {
                        aiGenerateInput.value = option.dataset.mode === 'ai' ? '1' : '0';
                    }
                    if (aiNoteSection) {
                        aiNoteSection.style.display = option.dataset.mode === 'ai' ? '' : 'none';
                    }
                });
            });

            teamSelect.addEventListener('change', refreshTemplatesForTeam);
            refreshTemplatesForTeam();
        })();
    </script>
    @endpush
@endif
@endsection
