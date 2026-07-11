@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><i class="fas fa-user-tag me-1"></i> Add Team Member Role</strong>
                    <a href="{{ route('admin.team-member-roles.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.team-member-roles.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="e.g. Team Lead" required
                                   oninput="autoSlug(this.value)">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" id="slugField"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug') }}" placeholder="e.g. team-lead"
                                   pattern="[a-z0-9\-]+" title="Lowercase letters, numbers and hyphens only">
                            <div class="form-text">Auto-generated from name. Lowercase, hyphens only. Cannot be changed easily once members use it.</div>
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="{{ old('sort_order', 0) }}" min="0">
                            <div class="form-text">Lower numbers appear first in dropdowns.</div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_default" id="is_default" class="form-check-input" value="1"
                                       {{ old('is_default') ? 'checked' : '' }}>
                                <label for="is_default" class="form-check-label">Set as default role for new invitations</label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Permissions</label>
                            <div class="border rounded p-3">
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleAll(true)">Select All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="toggleAll(false)">Clear All</button>
                                </div>
                                @foreach ($availablePermissions as $key => $label)
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" id="perm_{{ $key }}"
                                               class="form-check-input perm-checkbox"
                                               value="{{ $key }}"
                                               {{ in_array($key, old('permissions', [])) ? 'checked' : '' }}>
                                        <label for="perm_{{ $key }}" class="form-check-label">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Role</button>
                            <a href="{{ route('admin.team-member-roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleAll(check) {
    document.querySelectorAll('.perm-checkbox').forEach(cb => cb.checked = check);
}
function autoSlug(name) {
    const field = document.getElementById('slugField');
    if (!field._edited) {
        field.value = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
    }
}
document.getElementById('slugField').addEventListener('input', () => {
    document.getElementById('slugField')._edited = true;
});
</script>
@endpush
