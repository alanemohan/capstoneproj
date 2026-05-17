<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role !== 'admin') {
            // Allow access to logout and specific pending pages if needed
            if ($request->routeIs('logout') || $request->routeIs('pending-approval')) {
                return $next($request);
            }

            if ($user->status === 'pending') {
                return redirect()->route('pending-approval')->with('status_message', 'Your account is pending admin approval.');
            }

            if ($user->status === 'rejected') {
                return redirect()->route('pending-approval')->with('status_message', 'Your teacher account has been rejected. Contact admin.');
            }
        }

        return $next($request);
    }
}
