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
                z-index: 1;
                pointer-events: none;
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

        <div class="overlay"></div>

        <div class="coming-soon-container">
            <h1>Coming Soon</h1>
            <p>Something amazing is on its way. Stay tuned!</p>

            {{-- Check if "Notify Me" is enabled --}}
            @if (\App\Models\Setting::get('notify_me'))
                <!-- Button to Open Modal -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#notifyMeModal">Notify Me</button>
            @endif
        </div>

        @if (\App\Models\Setting::get('notify_me'))
            <!-- Notify Me Modal -->
            <div class="modal fade" id="notifyMeModal" tabindex="-1" aria-labelledby="notifyMeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="notifyMeModalLabel">Stay Informed</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('notify.store') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" name="name" class="form-control" id="name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" id="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="company" class="form-label">Company (Optional)</label>
                                    <input type="text" name="company" class="form-control" id="company">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection
