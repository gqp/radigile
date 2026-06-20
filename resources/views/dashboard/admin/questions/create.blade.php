@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Create Question</h2>

        {{-- Flash success message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- General error messages --}}
        @if (!$errors->isEmpty() && !session('success'))
            @if ($errors->has('error'))
                <div class="alert alert-danger">
                    {{ $errors->first('error') }}
                </div>
            @else
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        @endif

        <form method="POST" action="{{ route('admin.questions.store') }}">
            @csrf
            <div class="mb-3">
                <label for="text" class="form-label">Question Text</label>
                <input type="text" class="form-control @error('text') is-invalid @enderror" id="text" name="text" value="{{ old('text') }}" required>
                @error('text')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                    <option value="" selected disabled>Select a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tags Input --}}
            <div class="mb-3">
                <label for="tags" class="form-label">Tags</label>
                <select class="form-control" id="tags" name="tags[]" multiple>
                    <!-- Populate existing tags -->
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->name }}" {{ old('tags') && in_array($tag->name, old('tags')) ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Add multiple tags by selecting from the dropdown or typing new tags.</small>
            </div>

            @for ($i = 0; $i <= 4; $i++)
                <div class="mb-3">
                    <label for="tip_{{ $i }}" class="form-label">Tip for Answer {{ $i }}</label>
                    <textarea class="form-control @error('tip_' . $i) is-invalid @enderror" id="tip_{{ $i }}" name="tip_{{ $i }}" rows="2">{{ old('tip_' . $i) }}</textarea>
                    @error('tip_' . $i)
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endfor

            <button type="submit" class="btn btn-primary">Create Question</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tagInput = new Choices('#tags', {
                removeItemButton: true,
                addItems: true, // Allow adding new items
                duplicateItemsAllowed: false,
                placeholderValue: 'Type and press Enter to add a tag',
            });
        });
    </script>
@endpush
