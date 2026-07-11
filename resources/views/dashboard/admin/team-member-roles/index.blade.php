@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-md-9">

            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong><i class="fas fa-user-tag me-1"></i> Team Member Roles</strong>
                    <a href="{{ route('admin.team-member-roles.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Role
                    </a>
                </div>

                @foreach (['success', 'error', 'info'] as $type)
                    @if (session($type))
                        <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} alert-dismissible fade show m-3 mb-0">
                            {{ session($type) }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                @endforeach

                <div class="card-body p-0">
                    @if ($roles->isEmpty())
                        <p class="text-muted p-3 mb-0">No roles defined yet.</p>
                    @else
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:50px">Order</th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Permissions</th>
                                    <th class="text-center">Default</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                <tr>
                                    <td class="text-muted small">{{ $role->sort_order }}</td>
                                    <td><strong>{{ $role->name }}</strong></td>
                                    <td><code>{{ $role->slug }}</code></td>
                                    <td>
                                        @forelse ($role->permissions ?? [] as $perm)
                                            <span class="badge bg-secondary me-1 mb-1">{{ $availablePermissions[$perm] ?? $perm }}</span>
                                        @empty
                                            <span class="text-muted small">None</span>
                                        @endforelse
                                    </td>
                                    <td class="text-center">
                                        @if ($role->is_default)
                                            <span class="badge bg-success">Default</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.team-member-roles.edit', $role) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                                        <form action="{{ route('admin.team-member-roles.destroy', $role) }}" method="POST" class="d-inline-block"
                                              onsubmit="return confirm('Delete role \'{{ $role->name }}\'? Existing members with this role will keep the slug label.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-header"><strong>About Roles</strong></div>
                <div class="card-body small text-muted">
                    <p>Roles are assigned to team members to reflect their responsibility in assessments (e.g. Team Lead, Observer).</p>
                    <p>The <strong>default</strong> role is pre-selected when inviting a new member.</p>
                    <p>Deleting a role does not remove it from existing team members — their stored slug is preserved.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
