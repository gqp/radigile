<!DOCTYPE html>
<html>
<head>
    <title>Team Invitation</title>
</head>
<body>
<p>You’ve been invited to join the team: <strong>{{ $teamName }}</strong>.</p>

<p>Click the link below to accept the invitation:</p>

<p>
    <a href="{{ $acceptUrl }}" style="font-size: 16px; color: #4CAF50; text-decoration: none;">
        Accept Invitation
    </a>
</p>

<p>If you didn’t request this, please ignore this email.</p>

<p>Alternatively, you can use the invite code below:</p>
<h3>Invite Code: {{ $code }}</h3>

<p>Thank you!</p>
</body>
</html>
