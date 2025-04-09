@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Edit Plan</h1>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.plans.update', $plan->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control">{{ old('description', $plan->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" name="price" id="price" class="form-control" step="0.01" value="{{ old('price', $plan->price) }}" required>
            </div>

            <div class="mb-3">
                <label for="interval" class="form-label">Interval</label>
                <select name="interval" id="interval" class="form-control" required>
                    <option value="monthly" {{ old('interval', $plan->interval) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ old('interval', $plan->interval) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    <option value="free" {{ old('interval', $plan->interval) === 'free' ? 'selected' : '' }}>Free</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="is_active" class="form-label">Status</label>
                <select name="is_active" id="is_active" class="form-control" required>
                    <option value="1" {{ old('is_active', $plan->is_active) == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active', $plan->is_active) == '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="d-flex justify-content-between mt-3">
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Save Changes</button>

                <!-- Cancel Button -->
                <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">Cancel</a>
            </div>

        </form>
    </div>
@endsection
