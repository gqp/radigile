@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                {{-- Settings Card --}}
                <div class="card">
                    <div class="card-header">
                        <h1>Hi Admin: {{ Auth::user()->name }}</h1>
                    </div>
                    <div class="card-body">
                        <h3>Settings</h3>

                        {{-- Invitation System Toggle --}}
                        <div class="mb-4">
                            <form action="{{ route('admin.invites.toggle') }}" method="POST" id="invitationSystemToggleForm">
                                @csrf
                                <div class="form-check form-switch">
                                    <label class="form-check-label" for="invitationSystemToggle">Invitation System</label>
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="invitationSystemToggle"
                                        name="status" <!-- Match the request key in your controller -->
                                    value="1"
                                    {{ \App\Models\Setting::get('invite_only') ? 'checked' : '' }}
                                    onchange="document.getElementById('invitationSystemToggleForm').submit();">
                                </div>
                            </form>
                        </div>

                        {{-- Notify Me System Toggle --}}
                        <div class="mb-4">
                            <form action="{{ route('admin.notify-me.toggle-global') }}" method="POST" id="notifyMeToggleForm">
                                @csrf
                                <div class="form-check form-switch">
                                    <label class="form-check-label" for="notifyMeToggle">Notify Me System</label>
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="notifyMeToggle"
                                        name="notify_me"
                                        value="1"
                                        {{ \App\Models\Setting::get('notify_me') ? 'checked' : '' }}
                                        onchange="document.getElementById('notifyMeToggleForm').submit();">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
