@extends('emails.layouts.branded')

@section('title', 'Verify Your Email Address')

@section('content')
    <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#2d2438;">
        Verify your email address 🌱
    </h1>

    <p style="margin:0 0 24px;">
        Hi {{ $name }}, please confirm this is your email address to finish setting up your Radigile account.
    </p>

    <x-email.button :url="$verificationUrl">Verify Email Address</x-email.button>

    <p style="margin:0; font-size:13px; color:#948b9e;">
        Didn't create a Radigile account? No action needed — you can safely ignore this email.
    </p>
@endsection
