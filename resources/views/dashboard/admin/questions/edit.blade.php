@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Question</h2>

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

        <form method="POST" action="{{ route('admin.questions.update', $question->id) }}">
            @csrf
            @method('PUT')

            {{-- Question Text --}}
            <div class="mb-3">
                <label for="text" class="form-label">Question Text</label>
                <input type="text" class="form-control @error('text') is-invalid @enderror" id="text" name="text" value="{{ old('text', $question->text) }}" required>
                @error('text')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Category --}}
            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                    <option value="" disabled>Select a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $question->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tags --}}
            <div class="mb-3">
                <label for="tags" class="form-label">Tags</label>
                <select class="form-control @error('tags') is-invalid @enderror" id="tags" name="tags[]" multiple>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->name }}"
                                @if (old('tags', $question->tags->pluck('name')->toArray()) && in_array($tag->name, old('tags', $question->tags->pluck('name')->toArray())))
                                    selected
                            @endif
                        >
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
                @error('tags')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Add multiple tags by selecting from the dropdown or typing new tags.</small>
            </div>

            <div class="mb-3">
                <label for="new_tag" class="form-label">Add New Tag</label>
                <input type="text" class="form-control @error('new_tag') is-invalid @enderror" id="new_tag" name="new_tag" placeholder="Enter a new tag to add to the system">
                @error('new_tag')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Admins can add a new tag to the system here.</small>
            </div>

            {{-- Additional Tips --}}
            @for ($i = 0; $i <= 4; $i++)
                <div class="mb-3">
                    <label for="tip_{{ $i }}" class="form-label">Tip for Answer {{ $i }}</label>
                    <textarea class="form-control @error('tip_' . $i) is-invalid @enderror" id="tip_{{ $i }}" name="tip_{{ $i }}" rows="2">{{ old('tip_' . $i, $question->{'tip_' . $i}) }}</textarea>
                    @error('tip_' . $i)
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endfor

            <button type="submit" class="btn btn-primary">Update Question</button>
        </form>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css">
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tagInput = new Choices('#tags', {
                removeItemButton: true,  // Enable removal
                addItems: true,         // Allow dynamic addition
                duplicateItemsAllowed: false, // Prevent duplicate tags
                placeholderValue: 'Type and press Enter to add a tag',
            });
        });
    </script>
@endpush

