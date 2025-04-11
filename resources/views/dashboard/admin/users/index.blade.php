@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    {{-- Card Header --}}
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-people-fill"></i> User Management</h4>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-plus-circle"></i> Create User
                        </a>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">
                        @if ($users->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Email Verified</th>
                                        <th>Active</th>
                                        <th>Role(s)</th>
                                        <th>Subscription</th>
                                        <th>Last Login</th>
                                        <th>Created</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            {{-- User Name --}}
                                            <td>{{ $user->name }}</td>

                                            {{-- User Email --}}
                                            <td>{{ $user->email }}</td>

                                            {{-- Email Verified --}}
                                            <td>
                                                @if ($user->email_verified_at)
                                                    <span class="badge bg-success">Verified</span>
                                                @else
                                                    <span class="badge bg-danger">Not Verified</span>
                                                @endif
                                            </td>

                                            {{-- Active Status --}}
                                            <td>
                                                <form method="POST"
                                                      action="{{ route('admin.users.toggle-active', $user->id) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="btn btn-sm {{ $user->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                        {{ $user->is_active ? 'Active' : 'Inactive' }}
                                                    </button>
                                                </form>
                                            </td>

                                            {{-- Assigned Roles --}}
                                            <td>
                                                {{ $user->roles->pluck('name')->join(', ') ?: 'None' }}
                                            </td>

                                            {{-- Subscription --}}
                                            <td>
                                                @if ($user->subscription)
                                                    <span class="badge bg-primary">
                                                        {{ $user->subscription->plan->name }}
                                                    </span>
                                                    <div>
                                                        <small>Starts: {{ optional($user->subscription->starts_at)->format('d-m-Y') }}</small><br>
                                                        <small>Ends: {{ optional($user->subscription->ends_at)->format('d-m-Y') }}</small>
                                                    </div>
                                                @else
                                                    <span class="badge bg-secondary">No Subscription</span>
                                                @endif
                                            </td>

                                            {{-- Last Login --}}
                                            <td>{{ optional($user->last_login_at)->format('d-m-Y H:i') ?? 'Never' }}</td>

                                            {{-- Created At --}}
                                            <td>{{ $user->created_at->format('d-m-Y H:i') }}</td>

                                            {{-- Actions --}}
                                            <td class="text-center">
                                                <a href="{{ route('admin.users.edit', $user->id) }}"
                                                   class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                                <form method="POST"
                                                      action="{{ route('admin.users.destroy', $user->id) }}"
                                                      class="d-inline">
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
                            <div class="alert alert-warning text-center">
                                <i class="bi bi-exclamation-circle"></i> No users found.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
