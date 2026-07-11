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
                        <h4 class="mb-0"><i class="bi bi-plus-circle"></i> Create New Plan</h4>
                        <a href="{{ route('admin.plans.index') }}" class="btn btn-outline-light btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Plans
                        </a>
                    </div>

                    {{-- Card Body --}}
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.plans.store') }}">
                            @csrf

                            {{-- Plan Name --}}
                            <div class="mb-3">
                                <label for="name" class="form-label">Plan Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Plan Description --}}
                            <div class="mb-3">
                                <label for="description" class="form-label">Plan Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                @error('description')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Plan Price --}}
                            <div class="mb-3">
                                <label for="price" class="form-label">Price (USD)</label>
                                <input type="number" class="form-control" id="price" name="price" value="{{ old('price', 0) }}" step="0.01" min="0" required>
                                @error('price')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Plan Interval --}}
                            <div class="mb-3">
                                <label for="interval" class="form-label">Billing Interval</label>
                                <select class="form-control" id="interval" name="interval" required>
                                    <option value="" disabled selected>Select Interval</option>
                                    <option value="monthly" {{ old('interval') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="yearly" {{ old('interval') == 'yearly' ? 'selected' : '' }}>Yearly</option>
                                    <option value="lifetime" {{ old('interval') == 'lifetime' ? 'selected' : '' }}>Lifetime</option>
                                    <option value="free" {{ old('interval') == 'free' ? 'selected' : '' }}>Free</option>
                                </select>
                                @error('interval')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Stripe Price ID --}}
                            <div class="mb-3">
                                <label for="stripe_price_id" class="form-label">Stripe Price ID <small class="text-muted">(leave blank for free plan)</small></label>
                                <input type="text" class="form-control" id="stripe_price_id" name="stripe_price_id" value="{{ old('stripe_price_id') }}" placeholder="price_1ABC...">
                                @error('stripe_price_id')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Limits --}}
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="max_teams" class="form-label">Max Teams</label>
                                    <input type="number" class="form-control" id="max_teams" name="max_teams" value="{{ old('max_teams', 1) }}" min="0" required>
                                    <small class="text-muted">Max number of teams this user can own.</small>
                                    @error('max_teams')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="max_members" class="form-label">Max Members per Team</label>
                                    <input type="number" class="form-control" id="max_members" name="max_members" value="{{ old('max_members', 5) }}" min="0" required>
                                    <small class="text-muted">Max members allowed in each team.</small>
                                    @error('max_members')
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            {{-- Plan Status --}}
                            <div class="mb-3">
                                <label for="is_active" class="form-label">Plan Status</label>
                                <select class="form-control" id="is_active" name="is_active" required>
                                    <option value="1" {{ old('is_active') === '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('is_active')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Feature Toggles --}}
                            <div class="mb-4">
                                <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                    <span>Plan Features</span>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAllFeatures()">Toggle All</button>
                                </label>
                                <div class="row g-2 mt-1">
                                    @foreach ($availableFeatures as $key => $label)
                                        <div class="col-md-6">
                                            <div class="form-check p-3 border rounded">
                                                <input class="form-check-input feature-check" type="checkbox"
                                                       name="features[]" value="{{ $key }}"
                                                       id="feature_{{ $key }}"
                                                       {{ in_array($key, old('features', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label w-100" for="feature_{{ $key }}">
                                                    <strong class="d-block">{{ $label }}</strong>
                                                    <small class="text-muted">{{ $key }}</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @error('features')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Create Plan</button>
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
