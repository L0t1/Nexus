<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SetLandingLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Check query parameter first, then session, then browser
        if ($request->has('lang') && in_array($request->query('lang'), ['en', 'sq'])) {
            $locale = $request->query('lang');
            session(['landing_locale' => $locale]);
            app()->setLocale($locale);
        } elseif (session()->has('landing_locale')) {
            app()->setLocale(session('landing_locale'));
        }

        return $next($request);
    }
}
