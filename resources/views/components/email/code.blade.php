@props(['label' => 'Or use this invite code:'])

<p style="margin:0 0 8px; font-size:14px; color:#6b6178;">{{ $label }}</p>
<p style="margin:0 0 24px;">
    <span style="display:inline-block; padding:10px 18px; background-color:#f4f1fb; border:1px solid #e4dbfa; border-radius:6px; font-size:16px; font-weight:700; letter-spacing:1px; color:#7c3aed;">
        {{ $slot }}
    </span>
</p>
