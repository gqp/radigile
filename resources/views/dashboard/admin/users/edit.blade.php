@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                {{-- Edit Role Card --}}
                <div class="card shadow-sm">
                    {{-- Card Header --}}
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square"></i> Edit Role: {{ $role->name }}
                        </h4>
                        <a href="{{ route('admin.roles.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Roles
                        </a>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                            @csrf
                            @method('PUT')

                            {{-- Role Name --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Role Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    value="{{ $role->name }}"
                                    required
                                >
                            </div>

                            {{-- Permissions --}}
                            <div class="mb-3">
                                <h5>Assign Permissions</h5>
                                @foreach ($permissions as $permission)
                                    <div class="form-check">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            name="permissions[]"
                                            id="permission-{{ $permission->id }}"
                                            value="{{ $permission->id }}"
                                            {{ $role->permissions->contains('name', $permission->name) ? 'checked' : '' }}
                                        >
                                        <label class="form-check-label" for="permission-{{ $permission->id }}">{{ $permission->name }}</label>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Actions --}}
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Save Changes
                                </button>
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .btn-primary {
            background-color: #663399;
            border-color: #663399;
        }

        .btn-primary:hover {
            background-color: #552b81;
            border-color: #552b81;
        }
    </style>
@endpush
