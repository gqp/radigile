@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><i class="fas fa-user-tag me-1"></i> Edit Role: {{ $role->name }}</strong>
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

                    <form method="POST" action="{{ route('admin.team-member-roles.update', $role) }}">
                        @csrf @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $role->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug</label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug', $role->slug) }}"
                                   pattern="[a-z0-9\-]+" title="Lowercase letters, numbers and hyphens only">
                            <div class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                Changing the slug will cause existing team members who were assigned this role to show the raw slug instead of the label until re-assigned.
                            </div>
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control"
                                   value="{{ old('sort_order', $role->sort_order) }}" min="0">
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_default" id="is_default" class="form-check-input" value="1"
                                       {{ old('is_default', $role->is_default) ? 'checked' : '' }}>
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
                                @php $currentPerms = old('permissions', $role->permissions ?? []); @endphp
                                @foreach ($availablePermissions as $key => $label)
                                    <div class="form-check">
                                        <input type="checkbox" name="permissions[]" id="perm_{{ $key }}"
                                               class="form-check-input perm-checkbox"
                                               value="{{ $key }}"
                                               {{ in_array($key, $currentPerms) ? 'checked' : '' }}>
                                        <label for="perm_{{ $key }}" class="form-check-label">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
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
</script>
@endpush
