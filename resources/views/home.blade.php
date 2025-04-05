@extends('layouts.app')

@section('content')
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

<!-- Coming Soon Section -->
<div class="coming-soon">
    <p>Our website is coming soon! Stay tuned.</p>
    <button class="btn btn-primary" onclick="alert('Thank you for your interest!')">Notify Me</button>
</div>

<!-- About Section -->
<div id="about" class="container mt-5">
    <h3>About Us</h3>
    <p class="text-muted">We are working hard to launch our new website. Stay connected and check back soon!</p>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection

