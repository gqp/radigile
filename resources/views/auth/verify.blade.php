@extends('layouts.app')

@section('content')
    <style>
        /* General Styles for Full-Width Layout */
        body {
            margin: 0;
            padding: 0;
            width: 100%;
        }

        .full-width-container {
            width: 100%;
            margin: 0;
            padding: 0;
        }

        /* Hero Block Styles */
        .hero-section {
            position: relative;
            width: 100%;
            height: 60vh; /* Adjust height as needed */
            background: url('{{ asset('imgs/verify_email.jpeg') }}') no-repeat center center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #fff;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Overlay for better text visibility */
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2; /* Ensure it sits above the overlay */
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .hero-content p {
            font-size: 1.25rem;
        }

        /* Content Section Styles */
        .content-section-container {
            width: 100%;
            padding: 2rem 10%; /* Wide spacing around the content; adjust as needed */
            background-color: #f8f9fa;
        }

        .content-section {
            margin-bottom: 2rem;
        }

        .about-heading {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            text-align: center;
            font-weight: bold;
        }

        .content-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.75rem;
        }

        .content-section p {
            font-size: 1rem;
            line-height: 1.6;
        }

        .content-section ul {
            margin-top: 1rem;
            padding-left: 1.5rem;
            list-style-type: none;
        }

        .content-section ul li {
            font-size: 1rem;
            margin-bottom: 0.75rem;
            position: relative;
        }

        .content-section ul li::before {
            content: '✅';
            position: absolute;
            left: -1.5rem;
            color: #28a745;
        }
    </style>

    <!-- Hero Section -->
    <section class="hero-section full-width-container">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Verify you Email</h1>
        </div>
    </section>

    <!-- Full-Width Content -->
    <div class="content-section-container">
        <div class="content-section">
            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    {{ __('A fresh verification link has been sent to your email address.') }}
                </div>
            @endif

            {{ __('Before proceeding, please check your email for a verification link.') }}
            {{ __('If you did not receive the email') }},
            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
            </form>
        </div>
    </div>
@endsection
