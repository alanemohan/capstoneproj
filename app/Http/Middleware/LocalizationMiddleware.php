<?php

namespace App\Http\Middleware;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocalizationMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $allowedLocales = ['en', 'hi', 'pa'];
        $locale = null;

        if ($request->query('lang') && in_array($request->query('lang'), $allowedLocales, true)) {
            $locale = $request->query('lang');
        }

        if (session()->has('locale') && in_array(session('locale'), $allowedLocales, true)) {
            $locale = session('locale');
        } elseif (session()->has('applocale') && in_array(session('applocale'), $allowedLocales, true)) {
            $locale = session('applocale');
        } elseif (Auth::check() && Auth::user()->language && in_array(Auth::user()->language, $allowedLocales, true)) {
            $locale = Auth::user()->language;
        }

        if ($locale) {
            app()->setLocale($locale);
            // Keep both legacy and current session keys synchronized.
            if (session('locale') !== $locale || session('applocale') !== $locale) {
                session(['locale' => $locale, 'applocale' => $locale]);
            }

            if (Auth::check() && Auth::user()->language !== $locale) {
                User::whereKey(Auth::id())->update(['language' => $locale]);
            }
        }

        return $next($request);
    }
}
