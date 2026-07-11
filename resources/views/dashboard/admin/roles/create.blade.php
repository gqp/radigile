@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0"><i class="fas fa-shield-alt"></i> Create Role</h4>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light"><strong>Role Details</strong></div>
                    <div class="card-body">
                        <div class="mb-0">
                            <label for="name" class="form-label fw-semibold">Role Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" placeholder="e.g. Content Manager" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <strong>Permissions</strong>
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAll()">Toggle All</button>
                    </div>
                    <div class="card-body">
                        @foreach ($permissions as $group => $groupPerms)
                            <p class="text-muted text-uppercase small fw-bold mb-2 mt-3 mb-0">{{ $group }}</p>
                            <div class="row g-2 mb-2">
                                @foreach ($groupPerms as $permission)
                                    <div class="col-md-6">
                                        <div class="form-check p-3 border rounded">
                                            <input class="form-check-input perm-check" type="checkbox"
                                                   name="permissions[]" value="{{ $permission->id }}"
                                                   id="perm_{{ $permission->id }}"
                                                   {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label w-100" for="perm_{{ $permission->id }}">
                                                <strong class="d-block">{{ $permission->name }}</strong>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Role
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleAll() {
    const boxes = document.querySelectorAll('.perm-check');
    const allChecked = [...boxes].every(b => b.checked);
    boxes.forEach(b => b.checked = !allChecked);
}
</script>
@endpush
