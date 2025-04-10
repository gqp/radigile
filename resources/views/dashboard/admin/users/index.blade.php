@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                {{-- Card for Roles Management --}}
                <div class="card shadow-sm">
                    {{-- Card Header --}}
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-shield-lock"></i> Roles Management
                        </h4>
                        <a href="{{ route('admin.roles.create') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-plus-circle"></i> Create Role
                        </a>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">
                        @if ($roles->isNotEmpty())
                            {{-- Roles Table --}}
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Role Name</th>
                                        <th>Permissions</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($roles as $role)
                                        <tr>
                                            <td>{{ $role->name }}</td>
                                            <td>{{ $role->permissions->pluck('name')->join(', ') }}</td>
                                            <td class="text-center">
                                                {{-- Edit Button --}}
                                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>

                                                {{-- Delete Button --}}
                                                <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            {{-- No Roles Alert --}}
                            <div class="alert alert-warning text-center">
                                <i class="bi bi-exclamation-circle"></i> No roles found.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
