@extends('emails.layouts.branded')

@section('title', 'Team Invitation')

@section('content')
    <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#2d2438;">
        You're invited to join {{ $teamName }} 🌱
    </h1>

    <p style="margin:0 0 24px;">
        You've been invited to join <strong>{{ $teamName }}</strong> on Radigile —
        accept below to start tracking and growing your team's agile maturity together.
    </p>

    <x-email.button :url="$acceptUrl">Accept Invitation</x-email.button>

    @if ($code)
        <x-email.code>{{ $code }}</x-email.code>
    @endif

    <p style="margin:0; font-size:13px; color:#948b9e;">
        Didn't expect this invite? No action needed — you can safely ignore this email.
    </p>
@endsection
