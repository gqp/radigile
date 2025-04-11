@extends('layouts.admin')

@section('content')
    <div class="app-container">
        <div class="container mt-5">
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
                                <form action="{{ route('admin.invites.toggle') }}" method="POST"
                                      id="invitationSystemToggleForm">
                                    @csrf
                                    <div class="d-flex align-items-center">
                                        <label for="invitationSystemToggle" class="me-3">
                                            <strong>Invitation System</strong>
                                            <br>
                                            <small class="text-muted">
                                                Allow only invited users to register.
                                            </small>
                                        </label>
                                        <div class="form-check form-switch ms-auto">
                                            <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    id="invitationSystemToggle"
                                                    name="invite_only"
                                                    value="1"
                                                    {{ \App\Models\Setting::get('invite_only') ? 'checked' : '' }}
                                                    onchange="document.getElementById('invitationSystemToggleForm').submit();">
                                        </div>
                                    </div>
                                </form>
                            </div>

                            {{-- Notify Me System Toggle --}}
                            <div class="mb-4">
                                <form action="{{ route('admin.notify-me.toggle-global') }}" method="POST"
                                      id="notifyMeToggleForm">
                                    @csrf
                                    <div class="d-flex align-items-center">
                                        <label for="notifyMeToggle" class="me-3">
                                            <strong>Notify Me System</strong>
                                            <br>
                                            <small class="text-muted">
                                                Enable or disable global notifications.
                                            </small>
                                        </label>
                                        <div class="form-check form-switch ms-auto">
                                            <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    id="notifyMeToggle"
                                                    name="notify_me"
                                                    value="1"
                                                    {{ \App\Models\Setting::get('notify_me') ? 'checked' : '' }}
                                                    onchange="document.getElementById('notifyMeToggleForm').submit();">
                                        </div>
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
    </div>
@endsection

@push('styles')
    <!-- Custom Styles for Admin Settings -->
    <style>
        .form-check-input:checked {
            background-color: #bb84e8; /* Custom toggle color for "on" state */
            border-color: #bb84e8; /* Matches toggle thumb color */
        }

        .form-check-input {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            position: relative;
            width: 2.75rem;
            height: 1.5rem;
            background-color: #eaeaea;
            border-radius: 1.25rem;
            transition: all 0.3s;
        }

        .form-check-input::before {
            content: "";
            position: absolute;
            top: 0.25rem;
            left: 0.25rem;
            width: 1rem;
            height: 1rem;
            background-color: #fff;
            border-radius: 50%;
            transition: all 0.3s;
        }

        .form-check-input:checked::before {
            left: 1.5rem; /* Move knob to the right */
        }

        .form-check-input:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(187, 132, 232, 0.25); /* Purple glow when focused */
        }

        .form-check-input:active::before {
            width: 1.15rem; /* Slightly enlarge thumb on click */
        }
    </style>
@endpush
