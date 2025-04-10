@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                {{-- Settings Card --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-gear-fill"></i> Admin Settings
                        </h4>
                        <span>Welcome, {{ Auth::user()->name }}</span>
                    </div>

                    <div class="card-body">
                        <h5 class="text-secondary">
                            <i class="bi bi-sliders"></i> System Settings
                        </h5>
                        <hr>

                        {{-- Invitation System Toggle --}}
                        <div class="mb-4">
                            <form action="{{ route('admin.invites.toggle') }}" method="POST" id="invitationSystemToggleForm">
                                @csrf
                                <div class="d-flex align-items-center">
                                    <label for="invitationSystemToggle" class="me-3">
                                        <strong>Invitation System</strong>
                                        <br>
                                        <small class="text-muted">
                                            Allow only invited users to register.
                                        </small>
                                    </label>
                                    {{-- Toggle Button --}}
                                    <button
                                        type="button"
                                        class="btn btn-toggle {{ \App\Models\Setting::get('invite_only') ? 'active' : '' }}"
                                        data-bs-toggle="button"
                                        aria-pressed="{{ \App\Models\Setting::get('invite_only') ? 'true' : 'false' }}"
                                        onclick="toggleSetting('invitationSystemToggleForm')">
                                        <span class="handle"></span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Notify Me System Toggle --}}
                        <div class="mb-4">
                            <form action="{{ route('admin.notify-me.toggle-global') }}" method="POST" id="notifyMeToggleForm">
                                @csrf
                                <div class="d-flex align-items-center">
                                    <label for="notifyMeToggle" class="me-3">
                                        <strong>Notify Me System</strong>
                                        <br>
                                        <small class="text-muted">
                                            Enable or disable global notifications.
                                        </small>
                                    </label>
                                    {{-- Toggle Button --}}
                                    <button
                                        type="button"
                                        class="btn btn-toggle {{ \App\Models\Setting::get('notify_me') ? 'active' : '' }}"
                                        data-bs-toggle="button"
                                        aria-pressed="{{ \App\Models\Setting::get('notify_me') ? 'true' : 'false' }}"
                                        onclick="toggleSetting('notifyMeToggleForm')">
                                        <span class="handle"></span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Additional Settings Placeholder --}}
                        <div class="mt-4">
                            <h6 class="text-secondary">
                                <i class="bi bi-wrench-adjustable"></i> Coming Soon!
                            </h6>
                            <p class="text-muted mb-0">
                                Add more settings here in the future...
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <!-- Custom Styles for Toggle Buttons -->
    <style>
        .btn-toggle {
            position: relative;
            display: inline-flex;
            align-items: center;
            width: 3rem;
            height: 1.5rem;
            background-color: #eaeaea;
            border: none;
            border-radius: 1.5rem;
            padding: 0;
            margin: 0;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-toggle.active {
            background-color: #bb84e8;
        }

        .btn-toggle .handle {
            content: "";
            position: absolute;
            width: 1.25rem;
            height: 1.25rem;
            background-color: white;
            border-radius: 50%;
            top: 0.125rem;
            left: 0.125rem;
            transition: all 0.3s;
        }

        .btn-toggle.active .handle {
            left: calc(100% - 1.375rem);
        }
    </style>
@endpush

@push('scripts')
    <script>
        function toggleSetting(formId) {
            const form = document.getElementById(formId);
            const button = form.querySelector('.btn-toggle');
            const isActive = button.classList.contains('active');

            // Toggle the button's active state
            button.classList.toggle('active');
            button.setAttribute('aria-pressed', !isActive);

            // Update the hidden input value
            const input = form.querySelector('input[type="checkbox"]');
            input.checked = !isActive;

            // Submit the form
            form.submit();
        }
    </script>
@endpush
