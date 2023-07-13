<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user() && (Auth::user()->role === 'admin' || Auth::user()->role === 'superadmin' || Auth::user()->role === 'developer')) {
            return $next($request);
        }
        return back()->with('danger_','clearance!');
    }
}
