@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <h2>Team Frameworks</h2>
            <a href="{{ route('admin.team-frameworks.create') }}" class="btn btn-primary">Add New Framework</a>
        </div>

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


        @if($frameworks->count())
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($frameworks as $framework)
                    <tr>
                        <td>{{ $framework->id }}</td>
                        <td>{{ $framework->name }}</td>
                        <td>{{ $framework->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.team-frameworks.edit', $framework->id) }}" class="btn btn-sm btn-warning">Edit</a>
                            <form action="{{ route('admin.team-frameworks.destroy', $framework->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @else
            <p class="text-muted">No frameworks found. <a href="{{ route('admin.team-frameworks.create') }}">Create one</a>.</p>
        @endif
    </div>
@endsection
