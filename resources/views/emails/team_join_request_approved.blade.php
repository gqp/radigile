@extends('emails.layouts.branded')

@section('title', "Welcome to {$teamName}")

@section('content')
    <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#2d2438;">
        You're in! 🌱
    </h1>

    <p style="margin:0 0 24px;">
        Hi {{ $requesterName }}, your request to join <strong>{{ $teamName }}</strong> has been approved.
        You're now a member.
    </p>

    <x-email.button :url="$teamUrl">Go to {{ $teamName }}</x-email.button>
@endsection
