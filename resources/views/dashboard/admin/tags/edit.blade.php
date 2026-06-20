@extends('layouts.admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Edit Tag') }}</div>

                    <div class="card-body">
                        <!-- Form Start -->
                        <form method="POST" action="{{ route('admin.tags.update', $tag->id) }}">
                            @csrf <!-- CSRF Token -->
                            @method('PUT') <!-- Define form method as PUT -->

                            <div class="form-group mb-3">
                                <label for="name">{{ __('Tag Name') }}</label>
                                <input
                                    type="text"
                                    name="name"
                                    id="name"
                                    class="form-control @error('name') is-invalid @enderror"
                                    value="{{ old('name', $tag->name) }}"
                                    required
                                    autofocus
                                    placeholder="Enter tag name">

                                <!-- Validation Error for Name -->
                                @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">{{ __('Description') }}</label>
                                <textarea
                                    name="description"
                                    id="description"
                                    class="form-control @error('description') is-invalid @enderror"
                                    rows="3"
                                    placeholder="Enter a short description of the tag">{{ old('description', $tag->description) }}</textarea>

                                <!-- Validation Error for Description -->
                                @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('Update Tag') }}</button>
                            <a href="{{ route('admin.tags.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        </form>
                        <!-- Form End -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
