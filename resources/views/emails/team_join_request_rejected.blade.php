@extends('emails.layouts.branded')

@section('title', "Update on your request to join {$teamName}")

@section('content')
    <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#2d2438;">
        Update on your request 🌱
    </h1>

    <p style="margin:0 0 24px;">
        Hi {{ $requesterName }}, your request to join <strong>{{ $teamName }}</strong> was declined.
    </p>

    <x-email.button :url="$browseUrl">Browse Other Teams</x-email.button>

    <p style="margin:0; font-size:13px; color:#948b9e;">
        You're welcome to request to join again later.
    </p>
@endsection
