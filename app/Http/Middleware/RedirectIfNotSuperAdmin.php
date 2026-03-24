<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Now running after Authenticate middleware, so auth()->user() is guaranteed if it reaches here.
        if (! auth()->user()?->isMainAdmin()) {
            return redirect('/user');
        }

        return $next($request);
    }
}
