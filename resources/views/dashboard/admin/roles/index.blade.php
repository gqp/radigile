@extends('layouts.app')

@section('content')
@include('layouts.partials.navbar')
<div class="container py-4">

    @foreach (['success', 'error'] as $type)
        @if (session($type))
            <div class="alert alert-{{ $type === 'error' ? 'danger' : 'success' }} alert-dismissible fade show">
                {{ session($type) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0"><i class="fas fa-shield-alt"></i> Roles & Permissions</h4>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New Role
        </a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            @if ($roles->isEmpty())
                <p class="text-muted p-3 mb-0">No roles found.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Role</th>
                                <th>Permissions</th>
                                <th class="text-center">Users</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                            <tr>
                                <td>
                                    <strong>{{ $role->name }}</strong>
                                    @if(in_array($role->name, ['Admin', 'User']))
                                        <span class="badge bg-secondary ms-1">system</span>
                                    @endif
                                </td>
                                <td>
                                    @forelse ($role->permissions as $perm)
                                        <span class="badge bg-light text-dark border me-1 mb-1">{{ $perm->name }}</span>
                                    @empty
                                        <span class="text-muted small">No permissions</span>
                                    @endforelse
                                </td>
                                <td class="text-center">{{ $role->users()->count() }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-pencil-alt"></i> Edit
                                    </a>
                                    @if(!in_array($role->name, ['Admin', 'User']))
                                        <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}" class="d-inline"
                                              onsubmit="return confirm('Delete the {{ $role->name }} role?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
