@extends('layouts.app')

@section('content')
<div class="app-container">
    <div class="container mt-5">
        <h1>Edit Subscription</h1>

        <form action="{{ route('admin.subscriptions.update', $subscription->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="plan_id" class="form-label">Plan</label>
                <select name="plan_id" id="plan_id" class="form-control">
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}" {{ $plan->id == $subscription->plan_id ? 'selected' : '' }}>
                            {{ $plan->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="starts_at" class="form-label">Start Date</label>
                <input type="datetime-local" name="starts_at" id="starts_at" class="form-control"
                       value="{{ old('starts_at', $subscription->starts_at->format('Y-m-d\TH:i')) }}">
            </div>

            <div class="mb-3">
                <label for="ends_at" class="form-label">End Date</label>
                <input type="datetime-local" name="ends_at" id="ends_at" class="form-control"
                       value="{{ old('ends_at', $subscription->ends_at ? $subscription->ends_at->format('Y-m-d\TH:i') : '') }}">
            </div>

            <div class="mb-3">
                <label for="is_active" class="form-label">Status</label>
                <select name="is_active" id="is_active" class="form-control">
                    <option value="1" {{ $subscription->is_active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$subscription->is_active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Save Changes</button>

                <!-- Cancel Button -->
                <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>
</div>
@endsection
