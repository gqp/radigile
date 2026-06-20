@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        Edit Team Domain

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
                    </div>

                    <div class="card-body">
                        <form action="{{ route('admin.team-domains.update', $domain->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="form-group mb-3">
                                <label for="name">Domain Name</label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $domain->name) }}" placeholder="Enter team domain name (e.g., DevOps, Product)">
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">Update Domain</button>
                            <a href="{{ route('admin.team-domains.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
