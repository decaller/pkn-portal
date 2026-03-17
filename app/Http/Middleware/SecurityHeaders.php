<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $directives = [
            'default-src' => "'self'",
            'script-src' => "'self' 'unsafe-inline' 'unsafe-eval' https://*",
            'style-src' => "'self' 'unsafe-inline' https://*",
            'img-src' => "'self' data: https://*",
            'font-src' => "'self' data: https://*",
            'connect-src' => "'self' https://*",
            'frame-src' => "'self' https://*",
        ];

        if (config('app.env') === 'local') {
            $viteUrls = 'http://localhost:* http://[::1]:* ws://localhost:* ws://[::1]:*';
            $directives['script-src'] .= " $viteUrls";
            $directives['style-src'] .= " $viteUrls";
            $directives['connect-src'] .= " $viteUrls";
        }

        $csp = implode('; ', array_map(
            fn ($directive, $value) => "$directive $value",
            array_keys($directives),
            $directives
        ));

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('Referrer-Policy', 'no-referrer-when-downgrade');
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}
