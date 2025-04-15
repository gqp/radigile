@extends('layouts.admin')

@section('content')
    <!-- Include Navbar -->
    @include('layouts.admin.navbar')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow-sm">
                    {{-- Card Header --}}
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-card-list"></i> Manage Plans</h4>
                        <a href="{{ route('admin.plans.create') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-plus-circle"></i> Create New Plan
                        </a>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">
                        @if ($plans->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                    <tr>
                                        <th>Name</th>
                                        <th>Description</th>
                                        <th>Price</th>
                                        <th>Interval</th>
                                        <th>Status</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach ($plans as $plan)
                                        <tr>
                                            <td>{{ $plan->name }}</td>
                                            <td>{{ $plan->description }}</td>
                                            <td>${{ number_format($plan->price, 2) }}</td>
                                            <td>{{ ucfirst($plan->interval) }}</td>
                                            <td>
                                                <span class="badge {{ $plan->is_active ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.plans.edit', $plan->id) }}"
                                                   class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.plans.edit', $plan->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="bi bi-pencil-square"></i> Edit
                                                </a>

                                                <form action="{{ route('admin.plans.destroy', $plan->id) }}" method="POST" style="display: inline-block;"
                                                      onsubmit="return confirm('Are you sure you want to delete this plan? This cannot be undone.');">
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
                                <i class="bi bi-exclamation-circle"></i> No plans found.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Include Bootstrap modal --}}
    <div class="modal fade" id="deletePlanModal" tabindex="-1" aria-labelledby="deletePlanModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePlanModalLabel">Delete Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this plan? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <form id="deletePlanForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deletePlanModal = document.getElementById('deletePlanModal');
            deletePlanModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const planId = button.getAttribute('data-bs-id');
                const form = deletePlanModal.querySelector('#deletePlanForm');
                form.action = `/admin/subscriptions/plans/${planId}`;
            });
        });
    </script>
@endsection
