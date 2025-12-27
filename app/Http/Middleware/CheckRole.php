<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user() || !in_array($request->user()->role, $roles)) {
            // Allow Admin to access everything? 
            // Ideally explicit is better, but admin usually overrides.
            // However, for this requirement "restrict roles", let's strict check.
            // If I do `CheckRole:pharmacist`, admin fails? 
            // Usually Admin should pass all checks.

            if ($request->user() && $request->user()->role === 'admin') {
                return $next($request);
            }

            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
