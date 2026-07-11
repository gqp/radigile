@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="fas fa-shield-alt"></i> Edit Role: <strong>{{ $role->name }}</strong>
                    @if(in_array($role->name, ['Admin', 'User']))
                        <span class="badge bg-secondary ms-2">system</span>
                    @endif
                </h4>
                <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-secondary btn-sm">
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

            <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                @csrf
                @method('PUT')

                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light"><strong>Role Details</strong></div>
                    <div class="card-body">
                        <div class="mb-0">
                            <label for="name" class="form-label fw-semibold">Role Name</label>
                            @if(in_array($role->name, ['Admin', 'User']))
                                <input type="text" class="form-control bg-light" value="{{ $role->name }}" disabled>
                                <input type="hidden" name="name" value="{{ $role->name }}">
                                <small class="text-muted">System role names cannot be changed.</small>
                            @else
                                <input type="text" name="name" id="name"
                                       class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name', $role->name) }}" required>
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
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
                            <p class="text-muted text-uppercase small fw-bold mb-2 mt-3">{{ $group }}</p>
                            <div class="row g-2 mb-2">
                                @foreach ($groupPerms as $permission)
                                    <div class="col-md-6">
                                        <div class="form-check p-3 border rounded">
                                            <input class="form-check-input perm-check" type="checkbox"
                                                   name="permissions[]" value="{{ $permission->id }}"
                                                   id="perm_{{ $permission->id }}"
                                                   {{ in_array($permission->id, $rolePermissionIds) ? 'checked' : '' }}>
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
                        <i class="fas fa-save"></i> Save Changes
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
