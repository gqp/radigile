@extends('emails.layouts.branded')

@section('title', "Join {$teamName} on Radigile")

@section('content')
    <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#2d2438;">
        You're invited to join {{ $teamName }} 🌱
    </h1>

    <p style="margin:0 0 20px;">
        Someone has invited you to join <strong>{{ $teamName }}</strong> on Radigile as
        a <strong>{{ ucfirst($role) }}</strong>.
    </p>

    @if ($inviteOnly)
        <x-email.callout>
            <strong>Radigile is currently invite-only.</strong>
            Your invitation link below includes an access code so you can create your account.
        </x-email.callout>
    @endif

    <p style="margin:0 0 24px;">To accept, you'll need to create a free Radigile account first — it only takes a minute.</p>

    <x-email.button :url="$registerUrl">Create Account &amp; Join Team</x-email.button>

    <p style="margin:0 0 24px; font-size:13px; color:#6b6178;">
        After registering, your team invitation will be waiting for you on your Teams page.
    </p>

    <p style="margin:0; font-size:13px; color:#948b9e;">
        Didn't expect this invite? No action needed — you can safely ignore this email.
    </p>
@endsection
