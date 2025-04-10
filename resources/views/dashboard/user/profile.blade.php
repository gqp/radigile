@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h1>Profile</h1>
                    </div>

                    <div class="card-body">
                        {{-- Personal Information --}}
                        <div>
                            <h4>Personal Information</h4>
                            {{-- Name Field with Edit Button --}}
                            <div class="d-flex align-items-center mb-3">
                                <strong>Name:</strong>
                                <span id="displayName" class="ms-2">{{ $user->name }}</span>
                                <button id="editNameButton" class="btn btn-sm btn-outline-secondary ms-3">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                            </div>

                            {{-- Hidden Form for Editing Name --}}
                            <form
                                action="{{ route('user.updateName') }}"
                                method="POST"
                                id="editNameForm"
                                class="d-none"
                            >
                                @csrf
                                @method('PUT')

                                <div class="d-flex align-items-center mb-3">
                                    <input
                                        type="text"
                                        name="name"
                                        id="nameInput"
                                        value="{{ old('name', $user->name) }}"
                                        class="form-control @error('name') is-invalid @enderror"
                                        style="max-width: 300px;"
                                    >
                                    <button class="btn btn-sm btn-primary ms-3" type="submit">Save</button>
                                    <button id="
