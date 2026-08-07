@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0"><i class="fas fa-plus-circle"></i> Create New Team</h4>
                <a href="{{ route('user.teams.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('user.teams.store') }}" id="createTeamForm">
                @csrf

                {{-- Team Details --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light"><strong>Team Details</strong></div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Team Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name') }}"
                                   placeholder="e.g. Product Growth Squad" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="2"
                                      placeholder="What does this team work on?">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="team_domain_id" class="form-label fw-semibold">Domain <span class="text-danger">*</span></label>
                                <select name="team_domain_id" id="team_domain_id"
                                        class="form-select @error('team_domain_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select a domain</option>
                                    @foreach($teamDomains as $domain)
                                        <option value="{{ $domain->id }}" {{ old('team_domain_id') == $domain->id ? 'selected' : '' }}>
                                            {{ $domain->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_domain_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label for="team_framework_id" class="form-label fw-semibold">Framework <span class="text-danger">*</span></label>
                                <select name="team_framework_id" id="team_framework_id"
                                        class="form-select @error('team_framework_id') is-invalid @enderror" required>
                                    <option value="" disabled selected>Select a framework</option>
                                    @foreach($teamFrameworks as $framework)
                                        <option value="{{ $framework->id }}" {{ old('team_framework_id') == $framework->id ? 'selected' : '' }}>
                                            {{ $framework->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('team_framework_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="mb-0">
                            <label for="type" class="form-label fw-semibold">Visibility</label>
                            <select name="type" id="type" class="form-select">
                                <option value="private" selected>Private — only invited members can see it</option>
                                <option value="public">Public — visible to all Radagile users</option>
                            </select>
                        </div>

                    </div>
                </div>

                {{-- Add Members --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <strong><i class="fas fa-users"></i> Add Members <span class="text-muted fw-normal">(optional)</span></strong>
                        <span id="memberCount" class="badge bg-primary" style="display:none"></span>
                    </div>
                    <div class="card-body">

                        {{-- Search box --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Search by name or email</label>
                            <div class="position-relative">
                                <input type="text" id="memberSearch" class="form-control"
                                       placeholder="e.g. Jane or jane@example.com" autocomplete="off">
                                <div id="searchResults"
                                     class="position-absolute w-100 bg-white border rounded shadow-sm mt-1"
                                     style="z-index:1000; display:none; max-height:220px; overflow-y:auto;"></div>
                            </div>
                            <div id="searchSpinner" class="text-muted small mt-1" style="display:none;">
                                <i class="fas fa-spinner fa-spin"></i> Searching...
                            </div>
                        </div>

                        {{-- Selected members list --}}
                        <div id="selectedMembers" style="display:none;">
                            <div class="d-flex flex-wrap gap-2" id="memberChips"></div>
                        </div>

                        {{-- Hidden inputs injected by JS --}}
                        <div id="memberInputs"></div>

                        <p class="text-muted small mb-0 mt-2">
                            Only registered Radagile users can be added here. To invite new users, visit the team page after creation.
                        </p>

                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fas fa-save"></i> Create Team
                    </button>
                    <a href="{{ route('user.teams.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const searchUrl    = '{{ route('user.search.users') }}';
const memberRoles  = @json($memberRoles->map(fn($r) => ['value' => $r->slug, 'label' => $r->name, 'default' => $r->is_default]));
const defaultRole  = memberRoles.find(r => r.default)?.value ?? memberRoles[0]?.value ?? 'member';
const searchInput  = document.getElementById('memberSearch');
const searchResults= document.getElementById('searchResults');
const searchSpinner= document.getElementById('searchSpinner');
const memberChips  = document.getElementById('memberChips');
const memberInputs = document.getElementById('memberInputs');
const selectedWrap = document.getElementById('selectedMembers');
const memberCount  = document.getElementById('memberCount');

// Map of id → {id, name, email, role}
const selected = {};

let debounceTimer;

searchInput.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    const q = searchInput.value.trim();
    if (q.length < 2) { searchResults.style.display = 'none'; return; }
    debounceTimer = setTimeout(() => doSearch(q), 300);
});

document.addEventListener('click', (e) => {
    if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
        searchResults.style.display = 'none';
    }
});

async function doSearch(q) {
    searchSpinner.style.display = 'block';
    searchResults.style.display = 'none';
    try {
        const res   = await fetch(`${searchUrl}?q=${encodeURIComponent(q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const users = await res.json();
        searchSpinner.style.display = 'none';
        renderDropdown(users);
    } catch {
        searchSpinner.style.display = 'none';
    }
}

function renderDropdown(users) {
    searchResults.innerHTML = '';

    if (users.length === 0) {
        searchResults.innerHTML = '<div class="px-3 py-2 text-muted small">No registered users found.</div>';
        searchResults.style.display = 'block';
        return;
    }

    users.forEach(user => {
        if (selected[user.id]) return; // already added

        const item = document.createElement('div');
        item.className = 'px-3 py-2 border-bottom d-flex align-items-center justify-content-between';
        item.style.cursor = 'pointer';
        item.innerHTML = `
            <div>
                <strong>${esc(user.name)}</strong>
                <small class="text-muted d-block">${esc(user.email)}</small>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary">Invite</button>
        `;
        item.addEventListener('click', () => addMember(user));
        item.addEventListener('mouseenter', () => item.classList.add('bg-light'));
        item.addEventListener('mouseleave', () => item.classList.remove('bg-light'));
        searchResults.appendChild(item);
    });

    searchResults.style.display = 'block';
}

function addMember(user) {
    if (selected[user.id]) return;

    selected[user.id] = { ...user, role: defaultRole };
    searchResults.style.display = 'none';
    searchInput.value = '';
    renderChips();
}

function removeMember(id) {
    delete selected[id];
    renderChips();
}

function renderChips() {
    memberChips.innerHTML  = '';
    memberInputs.innerHTML = '';
    const ids = Object.keys(selected);

    ids.forEach((id, index) => {
        const m = selected[id];

        // Chip
        const chip = document.createElement('div');
        chip.className = 'badge bg-light text-dark border d-flex align-items-center gap-2 py-2 px-3';
        chip.style.fontSize = '0.85rem';
        chip.innerHTML = `
            <span>${esc(m.name)}</span>
            <select class="form-select form-select-sm border-0 p-0 bg-transparent"
                    style="width:auto; font-size:0.8rem;"
                    onchange="selected[${id}].role = this.value; syncInputs()">
                ${memberRoles.map(r => `<option value="${r.value}" ${m.role === r.value ? 'selected' : ''}>${r.label}</option>`).join('')}
            </select>
            <button type="button" class="btn-close btn-close-sm" onclick="removeMember(${id})"></button>
        `;
        memberChips.appendChild(chip);

        // Hidden inputs
        const inputId   = document.createElement('input');
        inputId.type    = 'hidden';
        inputId.name    = `members[${index}][id]`;
        inputId.value   = m.id;

        const inputRole = document.createElement('input');
        inputRole.type  = 'hidden';
        inputRole.name  = `members[${index}][role]`;
        inputRole.value = m.role;
        inputRole.id    = `role_input_${id}`;

        memberInputs.appendChild(inputId);
        memberInputs.appendChild(inputRole);
    });

    const count = ids.length;
    selectedWrap.style.display = count > 0 ? 'block' : 'none';
    memberCount.textContent    = `${count} to invite`;
    memberCount.style.display  = count > 0 ? 'inline' : 'none';
}

function syncInputs() {
    // Re-render to sync role changes from dropdowns inside chips
    renderChips();
}

function esc(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endpush
