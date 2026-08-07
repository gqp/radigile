@extends('emails.layouts.branded')

@section('title', "Your template \"{$templateTitle}\" is now public")

@section('content')
    <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#2d2438;">
        Your template is now public 🌱
    </h1>

    <p style="margin:0 0 24px;">
        Hi {{ $requesterName }}, your assessment template "<strong>{{ $templateTitle }}</strong>" has been
        approved and is now available to every team.
    </p>

    <x-email.button :url="$teamUrl">View Your Team</x-email.button>
@endsection
