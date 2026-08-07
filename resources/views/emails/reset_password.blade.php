@extends('emails.layouts.branded')

@section('title', 'Reset Your Password')

@section('content')
    <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#2d2438;">
        Reset your password 🌱
    </h1>

    <p style="margin:0 0 24px;">
        Hi {{ $name }}, we received a request to reset the password for your Radagile account.
    </p>

    <x-email.button :url="$resetUrl">Reset Password</x-email.button>

    <p style="margin:0 0 24px; font-size:13px; color:#6b6178;">
        This password reset link will expire in {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire') }} minutes.
    </p>

    <p style="margin:0; font-size:13px; color:#948b9e;">
        Didn't request a password reset? No action needed — you can safely ignore this email.
    </p>
@endsection
