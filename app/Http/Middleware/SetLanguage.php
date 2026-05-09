<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\App;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (session()->has('applocale')) {
            App::setLocale(session()->get('applocale'));
        } elseif (auth()->check() && auth()->user()->language) {
            App::setLocale(auth()->user()->language);
            session(['applocale' => auth()->user()->language]);
        }

        return $next($request);
    }
}
