<!DOCTYPE html>
<html>
<head>
    <title>You're invited to register!</title>
</head>
<body>
<h1>Welcome to {{ config('app.name') }}</h1>
<p>You’ve been invited to join a team on our platform. Since you’re not registered yet, please complete your registration using the link below:</p>

<p><a href="{{ $registrationUrl }}">Click here to register</a></p>

<p>Alternatively, you can use the following invite code during registration: <strong>{{ $code }}</strong></p>

<p>If you didn’t request this or receive it by mistake, you can safely ignore this email.</p>

<p>Thank you,<br>The Team</p>
</body>
</html>
