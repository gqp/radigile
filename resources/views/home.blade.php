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

            <style>
                /* Fullscreen Video Background Style */
                .video-background {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100vw;
                    height: 100vh;
                    object-fit: cover;
                    z-index: -1;
                }

                .overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    /*background: rgba(0, 0, 0, 0.5); /* Dark overlay for text visibility */
                    z-index: 1;
                    pointer-events: none; /* Prevent overlay from intercepting clicks */
                }

                .coming-soon-container {
                    position: relative;
                    z-index: 2;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    text-align: center;
                    color: #fff;
                }

                .coming-soon-container h1 {
                    font-size: 3rem;
                    font-weight: bold;
                    color: #000;
                }

                .coming-soon-container p {
                    font-size: 1.5rem;
                    margin: 1rem 0;
                    color: #000;
                }

                .coming-soon-container .btn {
                    font-size: 1.25rem;
                    padding: 0.75rem 2rem;
                }
            </style>

            <!-- Video Background -->
            <video class="video-background" autoplay muted loop>
                <source src="{{ asset('vids/home.mp4') }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>

            <!-- Dark overlay for text readability -->
            <div class="overlay"></div>

            <!-- Coming Soon Content -->
            <div class="coming-soon-container">
                <h1>Coming Soon</h1>
                <p>Something amazing is on its way. Stay tuned!</p>
                <button class="btn btn-primary" onclick="alert('Thank you for your interest!')">Notify Me</button>
            </div>

@endsection

