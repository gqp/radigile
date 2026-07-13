@extends('emails.layouts.branded')

@section('title', "You're invited to register!")

@section('content')
    <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#2d2438;">
        Welcome to {{ config('app.name') }} 🌱
    </h1>

    <p style="margin:0 0 24px;">
        You've been invited to join a team on Radigile. Since you're not registered yet,
        create your account below to get started.
    </p>

    <x-email.button :url="$registrationUrl">Create Your Account</x-email.button>

    <x-email.code label="Or use this invite code when you register:">{{ $code }}</x-email.code>

    <p style="margin:0; font-size:13px; color:#948b9e;">
        Didn't expect this invite? No action needed — you can safely ignore this email.
    </p>
@endsection
