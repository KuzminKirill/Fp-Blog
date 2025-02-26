<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->header('Accept-Language', $request->query('lang', 'en'));
        if (in_array($locale, ['en', 'it'])) {
            App::setLocale($locale);
        }
        return $next($request);
    }
}
