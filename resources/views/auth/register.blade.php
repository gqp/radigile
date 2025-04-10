@extends('layouts.home')

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
            height: 30vh; /* Adjust height as needed */
            background: url('{{ asset('imgs/register.jpeg') }}') no-repeat center center/cover;
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

    <section class="hero-section full-width-container">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>Register</h1>
        </div>
    </section>
    <div class="content-section-container">
        <div class="content-section">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="row mb-3">
                    <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>
                    <div class="col-md-6">
                        <input id="name" type="text"
                               class="form-control @error('name') is-invalid @enderror" name="name"
                               value="{{ old('name') }}" required autocomplete="name" autofocus>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                        @enderror
                    </div>
                </div>

                <!-- Invite Code Field: Only display when invite-only mode is active -->
                @if($inviteOnly)
                    <div class="row mb-3">
                        <label for="invite_code" class="col-md-4 col-form-label text-md-end">{{ __('Invite Code') }}</label>
                        <div class="col-md-6">
                            <input id="invite_code" type="text"
                                   class="form-control @error('invite_code') is-invalid @enderror" name="invite_code"
                                   value="{{ old('invite_code') }}">
                            @error('invite_code')
                            <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="row mb-3">
                    <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>
                    <div class="col-md-6">
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror" name="email"
                               value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>
                    <div class="col-md-6">
                        <input id="password" type="password"
                               class="form-control @error('password') is-invalid @enderror" name="password"
                               required autocomplete="new-password">
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>
                    <div class="col-md-6">
                        <input id="password-confirm" type="password" class="form-control"
                               name="password_confirmation" required autocomplete="new-password">
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Register') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
