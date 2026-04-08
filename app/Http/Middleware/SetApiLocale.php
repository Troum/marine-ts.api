<?php

namespace App\Http\Middleware;

use App\Support\MarineLocale;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = MarineLocale::resolve(
            $request->query('locale'),
            $request->header('Accept-Language')
        );

        app()->setLocale($locale);

        return $next($request);
    }
}
