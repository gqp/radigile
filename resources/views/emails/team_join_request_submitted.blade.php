@extends('emails.layouts.branded')

@section('title', "New request to join {$teamName}")

@section('content')
    <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#2d2438;">
        New request to join {{ $teamName }} 🌱
    </h1>

    <p style="margin:0 0 24px;">
        Hi {{ $approverName }}, <strong>{{ $requesterName }}</strong> has requested to join
        <strong>{{ $teamName }}</strong> on Radagile.
    </p>

    @if ($requestMessage)
        <x-email.callout>
            <strong>{{ $requesterName }}</strong> wrote: "{{ $requestMessage }}"
        </x-email.callout>
    @endif

    <x-email.button :url="$reviewUrl">Review Request</x-email.button>

    <p style="margin:0; font-size:13px; color:#948b9e;">
        You're receiving this because you can approve join requests for this team.
    </p>
@endsection
