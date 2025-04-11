@extends('layouts.app')

@section('content')
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
                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
                            </div>

                            {{-- Description --}}
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control">{{ old('description', $plan->description) }}</textarea>
                            </div>

                            {{-- Price --}}
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" name="price" id="price" class="form-control" step="0.01" value="{{ old('price', $plan->price) }}" required>
                            </div>

                            {{-- Interval --}}
                            <div class="mb-3">
                                <label for="interval" class="form-label">Interval</label>
                                <select name="interval" id="interval" class="form-select" required>
                                    <option value="free" {{ old('interval', $plan->interval) === 'free' ? 'selected' : '' }}>Free</option>
                                    <option value="monthly" {{ old('interval', $plan->interval) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="yearly" {{ old('interval', $plan->interval) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                                </select>
                            </div>

                            {{-- Active Status --}}
                            <div class="mb-3">
                                <label for="is_active" class="form-label">Status</label>
                                <select name="is_active" id="is_active" class="form-select" required>
                                    <option value="1" {{ old('is_active', $plan->is_active) == '1' ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $plan->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                                </select>
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
