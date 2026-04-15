<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Политика CSP для дашборда Telescope и формы /telescope-auth/*.
 *
 * Если внешний прокси/nginx добавляет свой заголовок Content-Security-Policy после PHP,
 * в браузере действуют обе политики (пересечение) — тогда нужно убрать/ослабить CSP в nginx,
 * см. deploy/nginx-telescope-csp.conf.example
 */
class TelescopeDashboardHttpHeaders
{
    private const string CSP = <<<'CSP'
default-src 'self'; style-src 'self' 'unsafe-inline' https://fonts.bunny.net; style-src-elem 'self' 'unsafe-inline' https://fonts.bunny.net; script-src 'self' 'unsafe-inline' 'unsafe-eval'; font-src 'self' data: https://fonts.bunny.net; img-src 'self' data: https: blob:; connect-src 'self'; frame-ancestors 'none'; base-uri 'self'; form-action 'self'
CSP;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (str_starts_with($request->path(), 'telescope')) {
            $response->headers->remove('Content-Security-Policy');
            $response->headers->remove('Content-Security-Policy-Report-Only');
            $response->headers->set('Content-Security-Policy', trim(self::CSP));
        }

        return $response;
    }
}
