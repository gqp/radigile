@extends('emails.layouts.branded')

@section('title', "New template publish request: {$templateTitle}")

@section('content')
    <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#2d2438;">
        New template publish request 🌱
    </h1>

    <p style="margin:0 0 24px;">
        Hi {{ $adminName }}, <strong>{{ $requesterName }}</strong> from <strong>{{ $teamName }}</strong> has
        requested that the assessment template "<strong>{{ $templateTitle }}</strong>" be made public.
    </p>

    @if ($requestMessage)
        <x-email.callout>
            <strong>{{ $requesterName }}</strong> wrote: "{{ $requestMessage }}"
        </x-email.callout>
    @endif

    <x-email.button :url="$reviewUrl">Review Request</x-email.button>

    <p style="margin:0; font-size:13px; color:#948b9e;">
        You're receiving this because you have admin access.
    </p>
@endsection
