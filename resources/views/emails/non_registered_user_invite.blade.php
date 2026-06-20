<!DOCTYPE html>
<html>
<head>
    <title>You're invited to join a team!</title>
</head>
<body>
<h1>You’ve been invited to join the team: {{ $teamName }}</h1>
<p>Hello,</p>
<p>Someone has invited you to join the team <strong>{{ $teamName }}</strong> on our platform. Since you are not yet registered, please use the link below to register and accept the invitation:</p>

<p><a href="{{ $registrationUrl }}">Click here to register and join the team</a></p>

<p>Your invite code: <strong>{{ $inviteCode }}</strong></p>

<p>If you did not expect this email, please ignore it.</p>

<p>Best regards,<br>The Team</p>
</body>
</html>
