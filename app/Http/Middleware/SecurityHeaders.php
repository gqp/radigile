<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    /**
     * CSP allows 'unsafe-inline' for scripts/styles because the app relies heavily on
     * inline <script>/style="" blocks throughout its Blade views. It still blocks loading
     * scripts, styles, and frames from any origin outside this explicit allowlist.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        if ($request->secure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        // In local dev, Vite's dev server (HMR client, unbuilt JS/SCSS, and its
        // websocket) is served from a separate origin (same host, port 5173) and
        // must be allowlisted explicitly — 'self' does not cover a different port.
        $viteDevOrigin = null;
        if (app()->environment('local')) {
            $host = parse_url((string) config('app.url'), PHP_URL_HOST) ?: $request->getHost();
            $viteDevOrigin = 'https://' . $host . ':5173';
        }

        $response->headers->set('Content-Security-Policy', implode('; ', array_filter([
            "default-src 'self'",
            trim("script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com {$viteDevOrigin}"),
            trim("style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com https://fonts.bunny.net {$viteDevOrigin}"),
            "font-src 'self' data: https://cdnjs.cloudflare.com https://fonts.gstatic.com https://fonts.bunny.net",
            "img-src 'self' data: https:",
            trim("connect-src 'self' {$viteDevOrigin}" . ($viteDevOrigin ? ' ' . str_replace('https://', 'wss://', $viteDevOrigin) : '')),
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'self'",
        ])));

        return $response;
    }
}
