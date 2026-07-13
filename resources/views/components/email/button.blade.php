@props(['url'])

<table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
    <tr>
        <td align="center" style="border-radius:8px; background:linear-gradient(90deg,#a78bfa,#7c3aed);">
            <a href="{{ $url }}"
               style="display:inline-block; padding:14px 32px; font-size:16px; font-weight:700; color:#ffffff; text-decoration:none; border-radius:8px;">
                {{ $slot }}
            </a>
        </td>
    </tr>
</table>
