@extends('layouts.user')

@section('content')
    @include('layouts.user.navbar')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center">
                <div class="card shadow-sm">
                    <div class="card-body py-5">
                        <i class="fas fa-check-circle text-success" style="font-size: 4rem;"></i>
                        <h2 class="mt-3">Payment Successful!</h2>
                        <p class="text-muted">Your subscription has been activated. It may take a moment to reflect on your dashboard.</p>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-primary mt-3">Go to Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
