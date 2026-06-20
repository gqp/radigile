@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between mb-3">
            <h2>Create Team Framework</h2>

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

            <a href="{{ route('admin.team-frameworks.index') }}" class="btn btn-secondary">Back</a>
        </div>

        <form action="{{ route('admin.team-frameworks.store') }}" method="POST">
            @csrf

            <div class="form-group mb-3">
                <label for="name">Framework Name</label>
                <input
                    type="text"
                    name="name"
                    id="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    required>
                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">Save Framework</button>
        </form>
    </div>
@endsection
