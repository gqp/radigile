@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Question Category</h2>

        {{-- Flash success message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- General error messages (only show if no success message exists) --}}
        @if (!$errors->isEmpty() && !session('success'))
            @if ($errors->has('error')) {{-- Check if there's a general error --}}
            <div class="alert alert-danger">
                {{ $errors->first('error') }}
            </div>
            @else {{-- Show validation errors --}}
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif
        @endif

        <form method="POST" action="{{ route('admin.question-categories.update', $category->id) }}">
            @csrf
            @method('PUT') <!-- Use PUT method for updating -->

            <div class="mb-3">
                <label for="name" class="form-label">Category Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Update Category</button>
            <a href="{{ route('admin.question-categories.index') }}" class="btn btn-secondary">Back</a>
        </form>
    </div>
@endsection
