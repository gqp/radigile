<!DOCTYPE html>
<html>
<head>
    <title>Team Invitation</title>
</head>
<body>
<h1>You’ve Been Invited To Join the Team: {{ $teamName }}</h1>
<p>Hello,</p>
<p>You’ve been invited to join the team <strong>{{ $teamName }}</strong>.</p>

<p><a href="{{ $acceptUrl }}">Click here to accept the invitation</a></p>

<p>If you believe this email was sent to you by mistake, please ignore it.</p>

<p>Best, <br> The Team</p>
</body>
</html>
