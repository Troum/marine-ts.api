<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Снимает заголовки CSP с ответов Telescope, если они заданы приложением/прокси поверх Laravel.
 * Не влияет на CSP, внедряемые расширениями браузера (AdGuard и т.д.).
 */
class StripContentSecurityPolicyForTelescope
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->is('telescope') || $request->is('telescope/*')) {
            $response->headers->remove('Content-Security-Policy');
            $response->headers->remove('Content-Security-Policy-Report-Only');
        }

        return $response;
    }
}
