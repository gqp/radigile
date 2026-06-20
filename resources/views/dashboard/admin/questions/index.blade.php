@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Questions</h2>

        {{-- Flash success message --}}
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        {{-- Validation errors --}}
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

        <a href="{{ route('admin.questions.create') }}" class="btn btn-primary mb-3">Create New Question</a>

        <form method="GET" action="{{ route('admin.questions.index') }}">
            <div class="form-group">
                <label for="search">Search</label>
                <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Search questions or tags">
            </div>

            <div class="form-group">
                <label for="category">Category</label>
                <select name="category" id="category" class="form-control">
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="tags">Tags</label>
                <select name="tags[]" id="tags" class="form-control" multiple>
                    @foreach ($tags as $tag)
                        <option value="{{ $tag->name }}" {{ request('tags') && in_array($tag->name, request('tags')) ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Filter</button>
        </form>

        <!-- Render Questions Table -->
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Text</th>
                <th>Category</th>
                <th>Tags</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($questions as $question)
                <tr>
                    <td>{{ $question->id }}</td>
                    <td>{{ $question->text }}</td>
                    <td>{{ $question->category->name ?? 'No category assigned' }}</td>
                    <td>
                        @if ($question->tags->isNotEmpty())
                            @foreach ($question->tags as $tag)
                                <span class="badge bg-primary text-light">{{ $tag->name }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">No tags</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="mt-4">
            {{ $questions->links() }}
        </div>
    </div>
@endsection
