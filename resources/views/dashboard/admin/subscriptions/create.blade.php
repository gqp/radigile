@extends('layouts.app')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card shadow-sm">
                    {{-- Card Header --}}
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create Subscription</h4>
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Subscriptions
                        </a>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">
                        <form action="{{ route('admin.subscriptions.store') }}" method="POST">
                            @csrf

                            {{-- User --}}
                            <div class="mb-3">
                                <label for="user_id" class="form-label">User</label>
                                <select name="user_id" id="user_id" class="form-select" required>
                                    <option value="">Select a User</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Plan --}}
                            <div class="mb-3">
                                <label for="plan_id" class="form-label">Plan</label>
                                <select name="plan_id" id="plan_id" class="form-select" required>
                                    <option value="">Select a Plan</option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Starts At --}}
                            <div class="mb-3">
                                <label for="starts_at" class="form-label">Start Date</label>
                                <input type="datetime-local" name="starts_at" id="starts_at" class="form-control" required>
                            </div>

                            {{-- Ends At --}}
                            <div class="mb-3">
                                <label for="ends_at" class="form-label">End Date (Optional)</label>
                                <input type="datetime-local" name="ends_at" id="ends_at" class="form-control">
                            </div>

                            {{-- Status --}}
                            <div class="mb-3">
                                <label for="is_active" class="form-label">Status</label>
                                <select name="is_active" id="is_active" class="form-select" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Save Subscription
                                </button>
                                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">
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
