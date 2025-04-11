@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    {{-- Card Header --}}
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-clipboard-data"></i> Manage Subscriptions</h4>
                        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-plus-circle"></i> Create Subscription
                        </a>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">
                        @if ($subscriptions->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th>User</th>
                                        <th>Plan</th>
                                        <th>Starts At</th>
                                        <th>Ends At</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($subscriptions as $subscription)
                                        <tr>
                                            <td>{{ $subscription->user->name }}</td>
                                            <td>{{ $subscription->plan->name }}</td>
                                            <td>{{ $subscription->starts_at->format('Y-m-d') }}</td>
                                            <td>{{ $subscription->ends_at ? $subscription->ends_at->format('Y-m-d') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge {{ $subscription->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $subscription->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}"
                                                   class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning text-center">
                                <i class="bi bi-exclamation-circle"></i> No subscriptions found.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
