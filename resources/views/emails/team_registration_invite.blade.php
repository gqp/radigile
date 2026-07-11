<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Join {{ $teamName }} on Radigile</title>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; max-width: 600px; margin: 40px auto; padding: 0 20px; }
        .btn { display: inline-block; padding: 12px 28px; background: #0d6efd; color: #fff; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 16px 0; }
        .note { background: #f8f9fa; border-left: 4px solid #0d6efd; padding: 12px 16px; margin: 16px 0; font-size: 14px; }
        .footer { margin-top: 32px; font-size: 12px; color: #888; }
    </style>
</head>
<body>

<h2>You've been invited to join <strong>{{ $teamName }}</strong></h2>

<p>
    Someone has invited you to join the <strong>{{ $teamName }}</strong> team on Radigile as
    a <strong>{{ ucfirst($role) }}</strong>.
</p>

@if ($inviteOnly)
<div class="note">
    <strong>Radigile is currently invite-only.</strong>
    Your invitation link below includes an access code so you can create your account.
</div>
@endif

<p>To accept, you'll need to create a free Radigile account first — it only takes a minute.</p>

<a href="{{ $registerUrl }}" class="btn">Create Account &amp; Join Team</a>

<p style="font-size:13px; color:#666;">
    After registering, your team invitation will be waiting for you on your Teams page.
</p>

<div class="footer">
    <p>If you weren't expecting this invitation, you can safely ignore this email.</p>
</div>

</body>
</html>
