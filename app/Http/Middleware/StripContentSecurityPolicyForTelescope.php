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

        $path = $request->path();

        // telescope/* и telescope-auth/* (оба начинаются с «telescope…»)
        if (str_starts_with($path, 'telescope')) {
            $response->headers->remove('Content-Security-Policy');
            $response->headers->remove('Content-Security-Policy-Report-Only');
        }

        return $response;
    }
}
