@extends('layouts.app')

@section('content')
<div class="app-container">
    <div class="container mt-5">
        <h1>Create a New Plan</h1>
        <form action="{{ route('admin.plans.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="name" class="form-label">Plan Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price (in USD)</label>
                <input type="number" name="price" id="price" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label for="interval" class="form-label">Interval</label>
                <select name="interval" id="interval" class="form-select" required>
                    <option value="free">Free</option>
                    <option value="monthly">Monthly</option>
                    <option value="yearly">Yearly</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="is_active" class="form-label">Active</label>
                <select name="is_active" id="is_active" class="form-select" required>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
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
</div>
@endsection
