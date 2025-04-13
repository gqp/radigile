@extends('layouts.home')

@section('content')
    <div class="container mt-5">
        <h2>Reset Your Password</h2>
        <p>You must reset your password before proceeding.</p>

        @if (session('warning'))
            <div class="alert alert-warning">
                {{ session('warning') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.force.reset.process') }}">
            @csrf

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Set New Password</button>
        </form>
    </div>
@endsection
