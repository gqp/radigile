@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-question-circle"></i> Create Question</h4>
        <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- AI Generation Panel --}}
    <div class="card shadow-sm mb-4 border-primary">
        <div class="card-header bg-primary text-white d-flex align-items-center gap-2">
            <i class="fas fa-robot"></i>
            <strong>Generate with AI</strong>
            <span class="badge bg-light text-primary ms-1">Beta</span>
        </div>
        <div class="card-body">
            <p class="text-muted small mb-3">Describe what you want to assess and AI will generate a question with a full 0–4 maturity rubric. Review and edit before saving.</p>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">What competency do you want to assess? <span class="text-danger">*</span></label>
                    <textarea id="ai-description" class="form-control" rows="3"
                        placeholder="e.g. How well the team manages technical debt and keeps the codebase clean over time"></textarea>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Category context</label>
                    <select id="ai-category" class="form-select">
                        <option value="">— None —</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Helps AI tailor the question.</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Framework context</label>
                    <select id="ai-framework" class="form-select">
                        <option value="">— General —</option>
                        <option value="Scrum">Scrum</option>
                        <option value="Kanban">Kanban</option>
                        <option value="SAFe">SAFe</option>
                        <option value="XP (Extreme Programming)">XP</option>
                        <option value="Shape Up">Shape Up</option>
                    </select>
                </div>
            </div>

            <div class="mt-3 d-flex align-items-center gap-3">
                <button type="button" id="ai-generate-btn" class="btn btn-primary" onclick="generateQuestion()">
                    <i class="fas fa-robot"></i> Generate Question
                </button>
                <div id="ai-loading" class="d-none text-muted small">
                    <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                    Generating&hellip;
                </div>
                <div id="ai-error" class="d-none text-danger small"></div>
            </div>
        </div>
    </div>

    {{-- Question Form --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light"><strong><i class="fas fa-edit"></i> Question Details</strong></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.questions.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="text" class="form-label fw-semibold">Question Text <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('text') is-invalid @enderror"
                           id="text" name="text" value="{{ old('text') }}"
                           placeholder="e.g. How consistently does our team review and address technical debt?" required>
                    @error('text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="category_id" class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                            <option value="" disabled selected>Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="tags" class="form-label fw-semibold">Tags</label>
                        <select class="form-control" id="tags" name="tags[]" multiple>
                            @foreach ($tags as $tag)
                                <option value="{{ $tag->name }}" {{ old('tags') && in_array($tag->name, old('tags')) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="mb-3 text-muted"><i class="fas fa-layer-group"></i> Maturity Rubric (0–4)</h6>

                @php
                    $rubricLabels = [
                        0 => ['label' => 'Score 0 — Not doing it at all', 'color' => 'danger',  'icon' => 'fas fa-times-circle'],
                        1 => ['label' => 'Score 1 — Aware / beginning',   'color' => 'warning', 'icon' => 'fas fa-seedling'],
                        2 => ['label' => 'Score 2 — Developing',           'color' => 'info',    'icon' => 'fas fa-chart-line'],
                        3 => ['label' => 'Score 3 — Competent',            'color' => 'primary', 'icon' => 'fas fa-check-circle'],
                        4 => ['label' => 'Score 4 — Exemplary / mastery',  'color' => 'success', 'icon' => 'fas fa-star'],
                    ];
                @endphp

                @for ($i = 0; $i <= 4; $i++)
                    <div class="mb-3">
                        <label for="tip_{{ $i }}" class="form-label">
                            <i class="{{ $rubricLabels[$i]['icon'] }} text-{{ $rubricLabels[$i]['color'] }}"></i>
                            {{ $rubricLabels[$i]['label'] }}
                        </label>
                        <textarea class="form-control tip-field @error('tip_' . $i) is-invalid @enderror"
                                  id="tip_{{ $i }}" name="tip_{{ $i }}" rows="2"
                                  placeholder="Describe what this maturity level looks like in practice...">{{ old('tip_' . $i) }}</textarea>
                        @error('tip_' . $i)<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                @endfor

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Save Question
                    </button>
                    <a href="{{ route('admin.questions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
<script>
document.addEventListener('DOMContentLoaded', function () {
    new Choices('#tags', {
        removeItemButton: true,
        addItems: true,
        duplicateItemsAllowed: false,
        placeholderValue: 'Type and press Enter to add a tag',
    });
});

async function generateQuestion() {
    const description = document.getElementById('ai-description').value.trim();
    const category    = document.getElementById('ai-category').value;
    const framework   = document.getElementById('ai-framework').value;
    const btn         = document.getElementById('ai-generate-btn');
    const loading     = document.getElementById('ai-loading');
    const errorEl     = document.getElementById('ai-error');

    errorEl.classList.add('d-none');

    if (!description) {
        errorEl.textContent = 'Please describe what you want to assess.';
        errorEl.classList.remove('d-none');
        return;
    }

    btn.disabled = true;
    loading.classList.remove('d-none');

    try {
        const res = await fetch('{{ route('admin.questions.generate-ai') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ description, category, framework }),
        });

        const data = await res.json();

        if (!res.ok) {
            throw new Error(data.error || 'Unknown error');
        }

        // Fill in the form
        document.getElementById('text').value = data.text ?? '';
        for (let i = 0; i <= 4; i++) {
            document.getElementById(`tip_${i}`).value = data[`tip_${i}`] ?? '';
        }

        // Sync category select if AI category matches
        if (category) {
            const catSelect = document.getElementById('ai-category');
            const formCat   = document.getElementById('category_id');
            const catName   = catSelect.options[catSelect.selectedIndex].text;
            for (let opt of formCat.options) {
                if (opt.text === catName) { opt.selected = true; break; }
            }
        }

        // Flash the filled fields to signal AI-filled content
        ['text', 'tip_0', 'tip_1', 'tip_2', 'tip_3', 'tip_4'].forEach(id => {
            const el = document.getElementById(id);
            el.classList.add('border-primary', 'bg-light');
            setTimeout(() => el.classList.remove('border-primary', 'bg-light'), 2000);
        });

        // Scroll to the form
        document.getElementById('text').scrollIntoView({ behavior: 'smooth', block: 'center' });

    } catch (err) {
        errorEl.textContent = err.message || 'Something went wrong. Please try again.';
        errorEl.classList.remove('d-none');
    } finally {
        btn.disabled = false;
        loading.classList.add('d-none');
    }
}
</script>
@endpush
