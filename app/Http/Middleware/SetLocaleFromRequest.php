<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromRequest
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $supported = ['pt_BR', 'en'];

        $preferred = $request->getPreferredLanguage($supported);

        if (is_string($preferred) && $preferred !== '') {
            app()->setLocale($preferred);
        }

        return $next($request);
    }
}
