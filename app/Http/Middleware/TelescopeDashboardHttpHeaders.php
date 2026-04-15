<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Снимает Content-Security-Policy с ответов Telescope и /telescope-auth/*.
 *
 * Политика вроде default-src 'none' блокирует свои же CSS/JS и внешние шрифты. Если CSP
 * добавляет только nginx после PHP, правьте конфиг nginx, а не только это middleware.
 */
class TelescopeDashboardHttpHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (str_starts_with($request->path(), 'telescope')) {
            $response->headers->remove('Content-Security-Policy');
            $response->headers->remove('Content-Security-Policy-Report-Only');
        }

        return $response;
    }
}
