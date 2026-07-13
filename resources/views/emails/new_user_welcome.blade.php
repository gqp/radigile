@extends('emails.layouts.branded')

@section('title', $isTestUser ? 'Your Test Account Is Ready' : 'Welcome to Radigile')

@section('content')
    <h1 style="margin:0 0 16px; font-size:22px; font-weight:700; color:#2d2438;">
        Hi {{ $name }}, welcome to Radigile 🌱
    </h1>

    <p style="margin:0 0 24px;">
        {{ $isTestUser ? 'Your test user account has been created.' : 'Your account has been created.' }}
        Here are your login credentials:
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" style="width:100%; margin:0 0 24px; background-color:#f4f1fb; border:1px solid #e4dbfa; border-radius:8px;">
        <tr>
            <td style="padding:16px 20px;">
                <p style="margin:0 0 8px; font-size:14px; color:#6b6178;">Email</p>
                <p style="margin:0 0 16px; font-size:16px; font-weight:700; color:#2d2438;">{{ $email }}</p>
                <p style="margin:0 0 8px; font-size:14px; color:#6b6178;">Temporary Password</p>
                <p style="margin:0; font-size:16px; font-weight:700; color:#2d2438;">{{ $password }}</p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 24px;">
        Please log in and change your password. Your email will be automatically verified after the password reset.
    </p>

    <x-email.button :url="url('/login')">Log In</x-email.button>

    <p style="margin:0; font-size:13px; color:#948b9e;">
        Thank you for using Radigile!
    </p>
@endsection
