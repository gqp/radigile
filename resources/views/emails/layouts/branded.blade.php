<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>@yield('title', config('app.name'))</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f1fb; font-family:'Nunito', Helvetica, Arial, sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f1fb; padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 12px rgba(124,58,237,0.08);">

                {{-- Accent bar --}}
                <tr>
                    <td style="height:6px; background:linear-gradient(90deg,#a78bfa,#7c3aed);"></td>
                </tr>

                {{-- Header / logo --}}
                <tr>
                    <td align="center" style="padding:32px 32px 16px;">
                        <img src="{{ asset('imgs/logo-purple.png') }}" alt="Radagile.com" width="200" style="display:block; max-width:200px; height:auto;">
                    </td>
                </tr>

                {{-- Body --}}
                <tr>
                    <td style="padding:8px 40px 32px; color:#3f3350; font-size:16px; line-height:1.6;">
                        @yield('content')
                    </td>
                </tr>

                {{-- Footer --}}
                <tr>
                    <td style="padding:20px 40px; background-color:#f9f7fd; border-top:1px solid #ece5fb;" align="center">
                        <p style="margin:0; font-size:12px; color:#a79cb5;">
                            &copy; {{ date('Y') }} Radagile.com &mdash; A New Era of Team Growth
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
