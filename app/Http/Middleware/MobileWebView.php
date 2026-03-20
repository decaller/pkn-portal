<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Support\HtmlString;
use Symfony\Component\HttpFoundation\Response;

class MobileWebView
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($source = $request->query('source')) {
            session(['mobile_source' => $source]);
        }

        if (session('mobile_source') === 'mobile') {
            Filament::registerRenderHook(
                'panels::head.end',
                fn (): HtmlString => new HtmlString('
                    <style>
                        .fi-sidebar, 
                        .fi-sidebar-open, 
                        .fi-topbar, 
                        .fi-main-ctn-content > div > header,
                        .fi-breadcrumbs { 
                            display: none !important; 
                        }
                        .fi-main-ctn {
                            padding-inline-start: 0 !important;
                            margin-inline-start: 0 !important;
                            padding-top: 0 !important;
                        }
                        .fi-main {
                            padding-top: 0 !important;
                        }
                    </style>
                '),
            );
        }

        return $next($request);
    }
}
