@extends('layouts.app')

@section('content')
    <!-- Include Navbar -->
    @include('layouts.partials.navbar')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card shadow-sm">
                    {{-- Card Header --}}
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Edit Plan</h4>
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Plans
                        </a>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">
                        <form action="{{ route('admin.plans.update', $plan->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            {{-- Plan Name --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Plan Name</label>
                                <input type="text" name="name" id="name" class="form-control"
                                       value="{{ old('name', $plan->name) }}" required>
                            </div>

                            {{-- Description --}}
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description"
                                          class="form-control">{{ old('description', $plan->description) }}</textarea>
                            </div>

                            {{-- Price --}}
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" name="price" id="price" class="form-control" step="0.01"
                                       value="{{ old('price', $plan->price) }}" required>
                            </div>

                            {{-- Interval --}}
                            <div class="mb-3">
                                <label for="interval" class="form-label">Interval</label>
                                <select name="interval" id="interval" class="form-select" required>
                                    <option value="free" {{ old('interval', $plan->interval) === 'free' ? 'selected' : '' }}>Free</option>
                                    <option value="monthly" {{ old('interval', $plan->interval) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="yearly" {{ old('interval', $plan->interval) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    <option value="lifetime" {{ old('interval', $plan->interval) === 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                                </select>
                            </div>

                            {{-- Stripe Price ID --}}
                            <div class="mb-3">
                                <label for="stripe_price_id" class="form-label">Stripe Price ID <small class="text-muted">(leave blank for free plan)</small></label>
                                <input type="text" name="stripe_price_id" id="stripe_price_id" class="form-control"
                                       value="{{ old('stripe_price_id', $plan->stripe_price_id) }}" placeholder="price_1ABC...">
                                @error('stripe_price_id')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Limits --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="max_teams" class="form-label">Max Teams</label>
                                    <input type="number" class="form-control" id="max_teams" name="max_teams"
                                           value="{{ old('max_teams', $plan->max_teams) }}" min="0" required>
                                    <small class="text-muted">Max number of teams this user can own.</small>
                                    @error('max_teams')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="max_members" class="form-label">Max Members per Team</label>
                                    <input type="number" class="form-control" id="max_members" name="max_members"
                                           value="{{ old('max_members', $plan->max_members) }}" min="0" required>
                                    <small class="text-muted">Max members allowed in each team.</small>
                                    @error('max_members')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            {{-- Active Status --}}
                            <div class="mb-3">
                                <label for="is_active" class="form-label">Status</label>
                                <select name="is_active" id="is_active" class="form-select" required>
                                    <option value="1" {{ old('is_active', $plan->is_active) == '1' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="0" {{ old('is_active', $plan->is_active) == '0' ? 'selected' : '' }}>
                                        Inactive
                                    </option>
                                </select>
                            </div>

                            {{-- Feature Toggles --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                    <span>Plan Features</span>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAllFeatures()">Toggle All</button>
                                </label>
                                <div class="row g-2 mt-1">
                                    @foreach ($availableFeatures as $key => $label)
                                        @php
                                            $currentFeatures = old('features', $plan->features ?? []);
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="form-check p-3 border rounded">
                                                <input class="form-check-input feature-check" type="checkbox"
                                                       name="features[]" value="{{ $key }}"
                                                       id="feature_{{ $key }}"
                                                       {{ in_array($key, $currentFeatures) ? 'checked' : '' }}>
                                                <label class="form-check-label w-100" for="feature_{{ $key }}">
                                                    <strong class="d-block">{{ $label }}</strong>
                                                    <small class="text-muted">{{ $key }}</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Buttons --}}
                            <div class="d-flex justify-content-between mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Save Changes
                                </button>
                                <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">
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

@push('scripts')
<script>
function toggleAllFeatures() {
    const boxes = document.querySelectorAll('.feature-check');
    const allChecked = [...boxes].every(b => b.checked);
    boxes.forEach(b => b.checked = !allChecked);
}
</script>
@endpush
