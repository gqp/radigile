@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Question Categories</h2>

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

        <a href="{{ route('admin.question-categories.create') }}" class="btn btn-primary mb-3">Create New Category</a>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->description }}</td>
                    <td>
                        <a href="{{ route('admin.question-categories.edit', $category->id) }}" class="btn btn-info btn-sm">Edit</a>
                        <form action="{{ route('admin.question-categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No categories available</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
