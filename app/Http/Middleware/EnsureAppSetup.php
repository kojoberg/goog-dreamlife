<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class EnsureAppSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip setup check during testing
        if (app()->runningUnitTests()) {
            return $next($request);
        }

        // Check if DB has any users
        if (User::count() === 0) {
            // Allow access to setup routes to prevent redirect loop
            if (!$request->is('setup') && !$request->is('setup/*')) {
                return redirect()->route('setup.index');
            }
        } else {
            // If users exist, block access to setup routes (security)
            if ($request->is('setup') || $request->is('setup/*')) {
                return redirect()->route('login');
            }
        }

        return $next($request);
    }
}
